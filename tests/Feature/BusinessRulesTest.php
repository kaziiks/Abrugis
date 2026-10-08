<?php

namespace Tests\Feature;

use App\Mail\ApplicationSubmitted;
use App\Models\Application;
use App\Models\PavingType;
use App\Models\PortfolioInfo;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\PavingTypeSeeder;
use Database\Seeders\PortfolioInfoSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_schema_uses_english_table_and_column_names(): void
    {
        $this->assertTrue(Schema::hasTable('applications'));
        $this->assertTrue(Schema::hasColumn('applications', 'paving_type_id'));
        $this->assertTrue(Schema::hasTable('paving_types'));
        $this->assertTrue(Schema::hasTable('portfolio_images'));
        $this->assertTrue(Schema::hasTable('reviews'));
        $this->assertTrue(Schema::hasColumn('reviews', 'application_id'));
        $this->assertTrue(Schema::hasColumn('reviews', 'review'));
        $this->assertTrue(Schema::hasIndex('reviews', 'reviews_application_id_unique'));
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertTrue(Schema::hasColumn('applications', 'estimate_total'));
        $this->assertTrue(Schema::hasColumn('applications', 'estimate_details'));
        $this->assertFalse(Schema::hasTable('pieteikums'));
    }

    public function test_registration_sends_email_verification_and_restricts_client_routes_until_verified(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'New Client',
            'email' => 'new-client@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'new-client@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('form'))->assertRedirect(route('verification.notice'));
        $this->get(route('verification.notice'))->assertOk();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($verificationUrl)
            ->assertRedirect(route('form'));

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->get(route('form'))->assertOk();
    }

    public function test_unverified_user_can_resend_the_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_request_and_complete_a_password_reset(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
        $notification = Notification::sent($user, ResetPassword::class)->first();

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_password_reset_request_does_not_disclose_unknown_emails(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.test'])
            ->assertSessionHas('status', __('If an account exists for that email address, we have sent a password reset link.'));
    }

    public function test_admin_can_upload_and_delete_portfolio_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $pavingType = PavingType::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);

        $response = $this->actingAs($admin)->post(route('admin.portfolio.store'), [
            'paving_type_id' => $pavingType->id,
            'title' => 'Pagalma projekts',
            'city' => 'Rīga',
            'images' => [$image = UploadedFile::fake()->image('pagalms.jpg')],
        ]);

        $response->assertSessionHasNoErrors();
        $portfolio = PortfolioInfo::firstOrFail();
        $portfolioImage = $portfolio->images()->firstOrFail();

        $this->assertTrue(Storage::disk('public')->exists($portfolioImage->image_path));

        $this->actingAs($admin)
            ->delete(route('admin.portfolio.images.destroy', $portfolioImage))
            ->assertSessionHasNoErrors();

        $this->assertFalse(Storage::disk('public')->exists($portfolioImage->image_path));
        $this->assertDatabaseMissing('portfolio_images', ['id' => $portfolioImage->id]);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/portfolio-placeholder.svg');
    }

    public function test_missing_portfolio_image_files_render_the_placeholder(): void
    {
        Storage::fake('public');
        $pavingType = PavingType::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);
        $portfolio = PortfolioInfo::create([
            'paving_type_id' => $pavingType->id,
            'title' => 'Projekts bez attēla',
            'city' => 'Rīga',
        ]);
        $portfolio->images()->create(['image_path' => 'portfolio/missing.jpg']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/portfolio-placeholder.svg')
            ->assertDontSee('storage/portfolio/missing.jpg')
            ->assertDontSee('storage/pirmsunpec/pirmsbetona.jpg');
    }

    public function test_portfolio_seeder_skips_missing_image_files(): void
    {
        Storage::fake('public');
        $this->seed(PavingTypeSeeder::class);
        $this->seed(PortfolioInfoSeeder::class);

        $this->assertDatabaseCount('portfolio_info', 10);
        $this->assertDatabaseCount('portfolio_images', 0);
    }

    public function test_paving_type_seeder_is_idempotent_and_refreshes_seed_data(): void
    {
        $this->seed(PavingTypeSeeder::class);
        PavingType::where('name', 'Betona bruģis')->update(['price_per_m2' => 99.00]);

        $this->seed(PavingTypeSeeder::class);

        $this->assertDatabaseCount('paving_types', 3);
        $this->assertDatabaseHas('paving_types', [
            'name' => 'Betona bruģis',
            'price_per_m2' => 25.00,
        ]);
        $this->assertDatabaseHas('paving_types', [
            'name' => 'Klinkera bruģis',
            'price_per_m2' => 35.00,
        ]);
    }

    public function test_user_can_see_only_their_own_applications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownApplication = $this->makeTestApplication($user, 'Mans projekts');
        $otherApplication = $this->makeTestApplication($otherUser, 'Cita lietotāja projekts');

        $this->actingAs($user)
            ->get(route('applications'))
            ->assertOk()
            ->assertSee($ownApplication->project_description)
            ->assertDontSee($otherApplication->project_description);
    }

    public function test_client_and_admin_routes_enforce_role_boundaries(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('form'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('applications'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('calc'))
            ->assertForbidden();
    }

    public function test_application_submission_is_assigned_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->actingAs($user)->post(route('form.store'), [
            'client_name' => 'Anna Pirma',
            'client_email' => 'anna@example.com',
            'client_phone' => '+37120000000',
            'project_description' => 'Jauns pagalms',
        ])->assertRedirect(route('applications'));

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'client_email' => $user->email,
            'status' => 'new',
        ]);

        Mail::assertSent(ApplicationSubmitted::class, 2);
    }

    public function test_calculator_estimate_is_saved_with_the_application(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $pavingType = PavingType::create([
            'name' => 'Betona bruģis',
            'price_per_m2' => 25,
        ]);

        $this->post(route('calc.calculate'), [
            'area' => 10,
            'paving_id' => $pavingType->id,
            'base' => 'standard',
            'removal' => true,
        ])
            ->assertOk()
            ->assertSee(route('form'), false)
            ->assertSee('510,00');

        $this->actingAs($user)
            ->get(route('form'))
            ->assertOk()
            ->assertSee('510,00')
            ->assertSee('value="10"', false)
            ->assertSee('value="' . $pavingType->id . '"', false);

        $this->post(route('form.store'), [
            'client_name' => $user->name,
            'client_email' => 'forged@example.test',
            'client_phone' => '+37120000000',
            'project_description' => 'Application from calculator estimate',
        ])->assertRedirect(route('applications'));

        $application = Application::where('project_description', 'Application from calculator estimate')->firstOrFail();
        $this->assertSame($user->email, $application->client_email);
        $this->assertSame($pavingType->id, $application->paving_type_id);
        $this->assertSame('10.00', $application->area_m2);
        $this->assertSame('510.00', $application->estimate_total);
        $this->assertEquals(510, $application->estimate_details['total']);
        $this->assertSame('standard', $application->estimate_details['base']);
        $this->assertFalse(session()->has('calculator_estimate'));
    }

    public function test_date_is_reserved_only_after_an_application_is_approved(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $thirdUser = User::factory()->create();
        $requestedDate = now()->addWeek()->toDateString();

        $firstApplication = $this->makeTestApplication($firstUser, 'Pirma rezervācija');
        $firstApplication->update([
            'requested_date' => $requestedDate,
        ]);

        $this->actingAs($secondUser)
            ->post(route('form.store'), [
                'client_name' => 'Otrais klients',
                'client_email' => $secondUser->email,
                'client_phone' => '+37120000001',
                'requested_date' => $requestedDate,
                'project_description' => 'Otra rezervācija',
            ])
            ->assertRedirect(route('applications'));

        $secondApplication = Application::where('project_description', 'Otra rezervācija')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.applications.update', $firstApplication), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $this->actingAs($thirdUser)
            ->post(route('form.store'), [
                'client_name' => 'Trešais klients',
                'client_email' => $thirdUser->email,
                'client_phone' => '+37120000002',
                'requested_date' => $requestedDate,
                'project_description' => 'Trešā rezervācija',
            ])
            ->assertSessionHasErrors('requested_date');

        $this->assertDatabaseHas('applications', [
            'project_description' => 'Otra rezervācija',
            'status' => 'new',
        ]);
        $this->assertDatabaseMissing('applications', [
            'project_description' => 'Trešā rezervācija',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.applications.update', $firstApplication), ['status' => 'rejected'])
            ->assertSessionHasNoErrors();

        $this->actingAs($thirdUser)
            ->post(route('form.store'), [
                'client_name' => 'Trešais klients',
                'client_email' => $thirdUser->email,
                'client_phone' => '+37120000002',
                'requested_date' => $requestedDate,
                'project_description' => 'Trešā rezervācija',
            ])
            ->assertRedirect(route('applications'));

        $this->assertDatabaseHas('applications', [
            'id' => $secondApplication->id,
            'status' => 'new',
        ]);
    }

    public function test_application_submission_accepts_an_available_requested_date(): void
    {
        $user = User::factory()->create();
        Mail::fake();
        $requestedDate = now()->addWeek()->toDateString();

        $this->actingAs($user)
            ->post(route('form.store'), [
                'client_name' => $user->name,
                'client_email' => $user->email,
                'client_phone' => '+37120000000',
                'requested_date' => $requestedDate,
                'project_description' => 'Bruģēšanas darbi pagalmā',
            ])
            ->assertRedirect(route('applications'));

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'requested_date' => $requestedDate,
            'status' => 'new',
        ]);
    }

    public function test_client_calendar_marks_only_approved_or_completed_dates_as_occupied(): void
    {
        $user = User::factory()->create();
        $requestedDate = now()->addWeeks(2)->toDateString();
        $application = $this->makeTestApplication($user, 'Pending request');
        $application->update(['requested_date' => $requestedDate]);

        $this->actingAs($user)
            ->get(route('calendar', ['calendar_month' => substr($requestedDate, 0, 7)]))
            ->assertOk()
            ->assertDontSee('calendar-day-occupied');

        $application->update(['status' => 'approved']);

        $this->actingAs($user)
            ->get(route('calendar', ['calendar_month' => substr($requestedDate, 0, 7)]))
            ->assertOk()
            ->assertSee('calendar-day-occupied');
    }

    public function test_calculator_rejects_a_paving_id_not_in_the_catalog(): void
    {
        PavingType::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);

        $this->from(route('calc'))
            ->post(route('calc.calculate'), [
                'area' => 10,
                'paving_id' => 999,
                'base' => 'standard',
            ])
            ->assertSessionHasErrors('paving_id');
    }

    public function test_calculator_uses_a_valid_catalog_price(): void
    {
        $pavingType = PavingType::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);

        $this->post(route('calc.calculate'), [
            'area' => 10,
            'paving_id' => $pavingType->id,
            'base' => 'standard',
        ])
            ->assertOk()
            ->assertSee('Betona bruģis')
            ->assertSee('430,00');
    }

    public function test_calculator_ignores_client_submitted_prices(): void
    {
        $pavingType = PavingType::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);

        $this->post(route('calc.calculate'), [
            'area' => 10,
            'paving_id' => $pavingType->id,
            'paving_price' => 0.01,
            'base' => 'standard',
            'base_price' => 0.01,
            'removal' => true,
            'removal_price' => 0.01,
        ])
            ->assertOk()
            ->assertSee('510,00')
            ->assertSee('250,00')
            ->assertSee('180,00')
            ->assertSee('80,00');
    }

    public function test_admin_cannot_reactivate_an_application_for_an_occupied_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $requestedDate = now()->addWeeks(2)->toDateString();
        $this->makeTestApplication($admin, 'Apstiprināta rezervācija', 'approved')->update(['requested_date' => $requestedDate]);
        $rejectedApplication = $this->makeTestApplication($admin, 'Noraidīta rezervācija', 'rejected');
        $rejectedApplication->update(['requested_date' => $requestedDate]);

        $this->actingAs($admin)
            ->from(route('admin.applications'))
            ->patch(route('admin.applications.update', $rejectedApplication), ['status' => 'approved'])
            ->assertSessionHasErrors('requested_date');

        $this->assertDatabaseHas('applications', [
            'id' => $rejectedApplication->id,
            'status' => 'rejected',
        ]);
    }

    public function test_only_owner_can_leave_a_review_for_a_completed_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $application = $this->makeTestApplication($owner, 'Pabeigts projekts', 'completed');

        $this->actingAs($otherUser)
            ->post(route('reviews.store'), [
                'application_id' => $application->id,
                'rating' => 5,
                'review' => 'Lieliski.',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('reviews', 0);

        $this->actingAs($owner)
            ->post(route('reviews.store'), [
                'application_id' => $application->id,
                'rating' => 5,
                'review' => 'Lieliski.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'application_id' => $application->id,
            'author_name' => $application->client_name,
            'rating' => 5,
        ]);
    }

    public function test_only_completed_application_reviews_are_public_and_empty_reviews_have_no_fabricated_text(): void
    {
        $user = User::factory()->create();
        $completedApplication = $this->makeTestApplication($user, 'Completed with empty review', 'completed');
        Review::create([
            'application_id' => $completedApplication->id,
            'author_name' => $user->name,
            'rating' => 3,
            'review' => null,
        ]);

        $hiddenApplication = $this->makeTestApplication($user, 'No longer completed', 'completed');
        Review::create([
            'application_id' => $hiddenApplication->id,
            'author_name' => $user->name,
            'rating' => 5,
            'review' => 'Hidden after status change',
        ]);
        $hiddenApplication->update(['status' => 'rejected']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertDontSee('Great cooperation and quality work.')
            ->assertDontSee('Hidden after status change');
    }

    public function test_review_is_rejected_for_an_application_that_is_not_completed(): void
    {
        $user = User::factory()->create();
        $application = $this->makeTestApplication($user, 'Vēl nepabeigts projekts', 'approved');

        $this->actingAs($user)
            ->post(route('reviews.store'), [
                'application_id' => $application->id,
                'rating' => 5,
                'review' => 'Pārāk agri.',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_application_can_receive_only_one_review(): void
    {
        $user = User::factory()->create();
        $application = $this->makeTestApplication($user, 'Pabeigts projekts', 'completed');
        Review::create([
            'application_id' => $application->id,
            'author_name' => $application->client_name,
            'rating' => 4,
            'review' => 'Labs darbs.',
        ]);

        $this->actingAs($user)
            ->post(route('reviews.store'), [
                'application_id' => $application->id,
                'rating' => 5,
                'review' => 'Vēl viens vērtējums.',
            ])
            ->assertSessionHasErrors('application_id');

        $this->assertDatabaseCount('reviews', 1);
    }

    private function makeTestApplication(User $user, string $description, string $status = 'new'): Application
    {
        return Application::create([
            'user_id' => $user->id,
            'client_name' => $user->name,
            'client_email' => $user->email,
            'client_phone' => '+37120000000',
            'project_description' => $description,
            'status' => $status,
        ]);
    }
}
