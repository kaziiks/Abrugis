<?php

namespace Tests\Feature;

use App\Models\Pieteikums;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_can_log_in_and_open_dashboard(): void
    {
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => '12345678',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs(User::where('email', 'admin@example.com')->first());
    }

    public function test_admin_can_view_all_applications(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Pieteikums::create([
            'user_id' => $admin->id,
            'client_name' => 'Anna Pirma',
            'client_email' => 'anna@example.com',
            'client_phone' => '+37120000000',
            'project_description' => 'Pirmais pieteikums',
            'status' => 'new',
        ]);

        Pieteikums::create([
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
            ->assertSee('Pieteikumu inbox')
            ->assertSee('Anna Pirma')
            ->assertSee('Jānis Otrais');
    }
}
