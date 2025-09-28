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
        $faker = \Faker\Factory::create();
        
        // Get all constituent campuses and their extensions
        $constituentCampuses = \App\Models\Campus::constituent()->with(['extensionCampuses'])->get();
        
        // Create 10 student users for each constituent campus and its extensions
        foreach ($constituentCampuses as $constituent) {
            // Get all campuses under this constituent (constituent + extensions)
            $allCampuses = $constituent->getAllCampusesUnder();
            
            // Calculate how many students per campus to get 10 total
            $totalCampuses = $allCampuses->count();
            $studentsPerCampus = $totalCampuses > 0 ? intval(10 / $totalCampuses) : 0;
            $remainingStudents = 10 % $totalCampuses;
            
            $studentCount = 0;
            foreach ($allCampuses as $index => $campus) {
                // Calculate students for this campus
                $studentsForThisCampus = $studentsPerCampus;
                if ($index < $remainingStudents) {
                    $studentsForThisCampus += 1; // Distribute remaining students
                }
                
                // Create students for this campus
                for ($i = 0; $i < $studentsForThisCampus; $i++) {
                    $studentCount++;
                    $yearPrefix = $faker->numberBetween(20, 25);
                    $studentId = $faker->unique()->numberBetween(10000, 99999);
                    $studentEmail = sprintf("%02d-%05d@g.batstate-u.edu.ph", $yearPrefix, $studentId);
                    
                    User::create([
                        'name' => $faker->name(),
                        'email' => $studentEmail,
                        'email_verified_at' => now(),
                        'password' => Hash::make('password123'),
                        'role' => 'student',
                        'campus_id' => $campus->id,
                    ]);
                }
            }
        }

        // Create 1 SFAO Admin per constituent campus
        foreach ($constituentCampuses as $campus) {
            User::updateOrCreate(
                ['email' => "sfaoadmin{$campus->id}@g.batstate-u.edu.ph"],
                [
                    'name' => "SFAO Admin - {$campus->name}",
                    'password' => Hash::make('password123'),
                    'role' => 'sfao',
                    'campus_id' => $campus->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Permanent Central Admin
        User::updateOrCreate(
            ['email' => 'centraladmin@g.batstate-u.edu.ph'],
            [
                'name' => 'Central Admin',
                'password' => Hash::make('password123'),
                'role' => 'central',
                'campus_id' => 1, // Pablo Borbon campus
                'email_verified_at' => now(),
            ]
        );
    }
}
