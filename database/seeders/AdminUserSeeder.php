<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('abrugis.admin.password');
        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_PASSWORD to a unique value of at least 12 characters before seeding the admin user.');
        }

        User::updateOrCreate(
            ['email' => config('abrugis.admin.email')],
            [
                'name' => 'Admin',
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );
    }
}
