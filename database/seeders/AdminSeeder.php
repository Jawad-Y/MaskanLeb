<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@maskanleb.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@maskanleb.com',
                'password' => bcrypt('password'),
                'phone' => '+961 70 000000',
                'role' => 'admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
