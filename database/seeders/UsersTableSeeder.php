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

        // List of constituent campuses (branch_id => name)
        $constituents = [
            1 => 'Pablo Borbon',
            2 => 'Alangilan',
            3 => 'ARASOF-Nasugbu',
            4 => 'JPLPC-Malvar',
            5 => 'Lipa',
        ];

        // Create 1 SFAO Admin per constituent
        foreach ($constituents as $branchId => $campusName) {
            User::updateOrCreate(
                ['email' => "sfaoadmin{$branchId}@g.batstate-u.edu.ph"],
                [
                    'name' => "SFAO Admin - {$campusName}",
                    'password' => Hash::make('password123'), // change later
                    'role' => 'sfao',
                    'branch_id' => $branchId,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Permanent Central Admin
        User::updateOrCreate(
            ['email' => 'centraladmin@g.batstate-u.edu.ph'],
            [
                'name' => 'Central Admin',
                'password' => Hash::make('password123'), // change later
                'role' => 'central',
                'branch_id' => 1, // you may adjust which branch_id should own central admin
                'email_verified_at' => now(),
            ]
        );
    }
}
