<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed judiciaries and admin first
        $this->call([
            JudiciarySeeder::class,
            AdminSeeder::class,
        ]);

        // Create test users
        $owners = User::factory(5)->owner()->create();
        $renters = User::factory(10)->renter()->create();

        // Create apartments for each owner
        $owners->each(function (User $owner) {
            Apartment::factory(rand(2, 5))->create([
                'owner_id' => $owner->id,
            ]);
        });
    }
}
