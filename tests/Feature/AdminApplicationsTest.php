<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AdminApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_can_log_in_and_open_dashboard(): void
    {
        $this->seed(AdminUserSeeder::class);

        $this->post('/login', [
            'email' => config('abrugis.admin.email'),
            'password' => 'testing-admin-password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs(User::where('email', config('abrugis.admin.email'))->first());
    }

    public function test_seeding_admin_resets_password_from_environment_configuration(): void
    {
        $admin = User::factory()->create([
            'email' => config('abrugis.admin.email'),
            'role' => 'admin',
            'password' => 'old-admin-password',
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertTrue(password_verify('testing-admin-password', $admin->fresh()->password));
    }

    public function test_seeding_admin_requires_a_valid_configured_email(): void
    {
        config(['abrugis.admin.email' => null]);

        try {
            $this->seed(AdminUserSeeder::class);
            $this->fail('Seeding an admin without a configured email should fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Set ADMIN_EMAIL to a valid email address before seeding the admin user.',
                $exception->getMessage(),
            );
        }
    }

    public function test_admin_can_view_all_applications(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Application::create([
            'user_id' => $admin->id,
            'client_name' => 'Anna Pirma',
            'client_email' => 'anna@example.com',
            'client_phone' => '+37120000000',
            'project_description' => 'Pirmais pieteikums',
            'status' => 'new',
        ]);

        Application::create([
            'user_id' => $admin->id,
            'client_name' => 'Jānis Otrais',
            'client_email' => 'janis@example.com',
            'client_phone' => '+37120000001',
            'project_description' => 'Otrais pieteikums',
            'status' => 'contacted',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee(__('Consultation calendar'));

        $this->actingAs($admin)
            ->get(route('admin.applications'))
            ->assertOk()
            ->assertSee('Pieteikumu inbox')
            ->assertSee('Anna Pirma')
            ->assertSee('Jānis Otrais');
    }

    public function test_calendar_day_opens_applications_for_that_date(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Application::create([
            'user_id' => $admin->id,
            'client_name' => 'Anna Pirma',
            'client_email' => 'anna@example.com',
            'client_phone' => '+37120000000',
            'project_description' => 'Pieteikums izvēlētajam datumam',
            'requested_date' => '2026-10-15',
            'status' => 'new',
        ]);

        Application::create([
            'user_id' => $admin->id,
            'client_name' => 'Jānis Otrais',
            'client_email' => 'janis@example.com',
            'client_phone' => '+37120000001',
            'project_description' => 'Pieteikums citam datumam',
            'requested_date' => '2026-10-16',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->get('/admin?calendar_month=2026-10')
            ->assertOk()
            ->assertSee(route('admin.applications', ['requested_date' => '2026-10-15']), false);

        $this->actingAs($admin)
            ->get(route('admin.applications', ['requested_date' => '2026-10-15']))
            ->assertOk()
            ->assertSee('Anna Pirma')
            ->assertDontSee('Jānis Otrais')
            ->assertSee(__('Showing applications for :date', ['date' => '15.10.2026']));
    }
}
