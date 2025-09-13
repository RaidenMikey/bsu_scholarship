<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 student accounts
        User::factory()->count(10)->state([
            'role' => 'student',
        ])->create();

        // Permanent SFAO Admin
        User::updateOrCreate(
            ['email' => 'sfaoadmin@g.batstate-u.edu.ph'],
            [
                'name' => 'SFAO Admin',
                'password' => Hash::make('password123'), // change later
                'role' => 'sfao',
                'branch_id' => 1, // adjust depending on your branches table
                'email_verified_at' => now(),
            ]
        );

        // Permanent Central Admin
        User::updateOrCreate(
            ['email' => 'centraladmin@g.batstate-u.edu.ph'],
            [
                'name' => 'Central Admin',
                'password' => Hash::make('password123'), // change later
                'role' => 'central',
                'branch_id' => 1,
                'email_verified_at' => now(),
            ]
        );
    }
}
