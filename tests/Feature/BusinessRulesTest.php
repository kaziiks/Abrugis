<?php

namespace Tests\Feature;

use App\Models\Atsauksme;
use App\Models\BrugaVeids;
use App\Models\Pieteikums;
use App\Models\PortfolioBilde;
use App\Models\PortfolioInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_delete_portfolio_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $pavingType = BrugaVeids::create(['name' => 'Betona bruģis', 'price_per_m2' => 25]);

        $response = $this->actingAs($admin)->post(route('admin.portfolio.store'), [
            'bruga_veids_id' => $pavingType->id,
            'title' => 'Pagalma projekts',
            'city' => 'Rīga',
            'images' => [$image = UploadedFile::fake()->image('pagalms.jpg')],
        ]);

        $response->assertSessionHasNoErrors();
        $portfolio = PortfolioInfo::firstOrFail();
        $portfolioImage = $portfolio->bildes()->firstOrFail();

        $this->assertTrue(Storage::disk('public')->exists($portfolioImage->image_path));

        $this->actingAs($admin)
            ->delete(route('admin.portfolio.bildes.destroy', $portfolioImage))
            ->assertSessionHasNoErrors();

        $this->assertFalse(Storage::disk('public')->exists($portfolioImage->image_path));
        $this->assertDatabaseMissing('portfolio_bilde', ['id' => $portfolioImage->id]);
    }

    public function test_user_can_see_only_their_own_applications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownApplication = $this->createPieteikums($user, 'Mans projekts');
        $otherApplication = $this->createPieteikums($otherUser, 'Cita lietotāja projekts');

        $this->actingAs($user)
            ->get(route('form'))
            ->assertOk()
            ->assertSee($ownApplication->project_description)
            ->assertDontSee($otherApplication->project_description);
    }

    public function test_application_submission_is_assigned_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('form.store'), [
            'client_name' => 'Anna Pirma',
            'client_email' => 'anna@example.com',
            'client_phone' => '+37120000000',
            'project_description' => 'Jauns pagalms',
        ])->assertRedirect(route('form'));

        $this->assertDatabaseHas('pieteikums', [
            'user_id' => $user->id,
            'client_email' => 'anna@example.com',
            'status' => 'new',
        ]);
    }

    public function test_only_owner_can_leave_a_review_for_a_completed_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $application = $this->createPieteikums($owner, 'Pabeigts projekts', 'completed');

        $this->actingAs($otherUser)
            ->post(route('atsauksmes.store'), [
                'pieteikums_id' => $application->id,
                'rating' => 5,
                'atsauksme' => 'Lieliski.',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('atsauksme', 0);

        $this->actingAs($owner)
            ->post(route('atsauksmes.store'), [
                'pieteikums_id' => $application->id,
                'rating' => 5,
                'atsauksme' => 'Lieliski.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('atsauksme', [
            'pieteikums_id' => $application->id,
            'author_name' => $application->client_name,
            'rating' => 5,
        ]);
    }

    public function test_review_is_rejected_for_an_application_that_is_not_completed(): void
    {
        $user = User::factory()->create();
        $application = $this->createPieteikums($user, 'Vēl nepabeigts projekts', 'approved');

        $this->actingAs($user)
            ->post(route('atsauksmes.store'), [
                'pieteikums_id' => $application->id,
                'rating' => 5,
                'atsauksme' => 'Pārāk agri.',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('atsauksme', 0);
    }

    public function test_application_can_receive_only_one_review(): void
    {
        $user = User::factory()->create();
        $application = $this->createPieteikums($user, 'Pabeigts projekts', 'completed');
        Atsauksme::create([
            'pieteikums_id' => $application->id,
            'author_name' => $application->client_name,
            'rating' => 4,
            'atsauksme' => 'Labs darbs.',
        ]);

        $this->actingAs($user)
            ->post(route('atsauksmes.store'), [
                'pieteikums_id' => $application->id,
                'rating' => 5,
                'atsauksme' => 'Vēl viens vērtējums.',
            ])
            ->assertSessionHasErrors('pieteikums_id');

        $this->assertDatabaseCount('atsauksme', 1);
    }

    private function createPieteikums(User $user, string $description, string $status = 'new'): Pieteikums
    {
        return Pieteikums::create([
            'user_id' => $user->id,
            'client_name' => $user->name,
            'client_email' => $user->email,
            'client_phone' => '+37120000000',
            'project_description' => $description,
            'status' => $status,
        ]);
    }
}