<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Campus;
use App\Models\Scholarship;
use App\Models\Scholar;
use App\Models\Application;
use App\Models\StudentProfile;
use App\Models\RejectedApplicant;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MainSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_PH'); 
        $password = Hash::make('password123');
        $now = now();

        // =================================================================
        // 1. CENTRAL ADMIN
        // =================================================================
        $pabloBorbon = Campus::where('name', 'Pablo Borbon')->first(); 
        
        User::create([
            'name' => 'Central Admin',
            'first_name' => 'Central',
            'last_name' => 'Admin',
            'email' => 'test.central-admin@g.batstate-u.edu.ph',
            'password' => $password,
            'role' => 'central',
            'campus_id' => $pabloBorbon ? $pabloBorbon->id : 1, // Default to PB or 1
            'email_verified_at' => $now,
            'sr_code' => 'CENTRAL-ADMIN', // Placeholder
            'birthdate' => $now->copy()->subYears(35)->format('Y-m-d'),
            'sex' => 'Male',
            'contact_number' => '09123456789',
        ]);
        
        $this->command->info('Created Central Admin: test.central-admin@g.batstate-u.edu.ph');

        // =================================================================
        // 2. SFAO ADMINS (One per Constituent Campus)
        // =================================================================
        $constituentCampuses = Campus::where('type', 'constituent')->get();

        foreach ($constituentCampuses as $campus) {
             // Generate slug for email: "Pablo Borbon" -> "pablo-borbon"
             // But user request specifically said: "test.sfao-/campus name/@..."
             // Assuming "test.sfao-alangilan" for Alangilan, "test.sfao-pablo-borbon" etc.
             $slug = \Illuminate\Support\Str::slug($campus->name);
             $email = "test.sfao-{$slug}@g.batstate-u.edu.ph";
             
             User::create([
                'name' => "SFAO Admin {$campus->name}",
                'first_name' => 'SFAO',
                'last_name' => "Admin {$campus->name}",
                'email' => $email,
                'password' => $password,
                'role' => 'sfao',
                'campus_id' => $campus->id,
                'email_verified_at' => $now,
                'sr_code' => 'SFAO-' . strtoupper($slug),
                'birthdate' => $now->copy()->subYears($faker->numberBetween(25, 50))->format('Y-m-d'),
                'sex' => $faker->randomElement(['Male', 'Female']),
                'contact_number' => '09' . $faker->numberBetween(100000000, 999999999),
             ]);
             
             $this->command->info("Created SFAO Admin: $email");
        }

        // =================================================================
        // 3. STUDENTS (30 per Constituent Group)
        // =================================================================
        
        // Get all scholarships for applications
        $scholarships = Scholarship::all();
        if ($scholarships->isEmpty()) {
            $this->command->warn('No scholarships found. Running ScholarshipsTableSeeder...');
            $this->call(ScholarshipsTableSeeder::class);
            $scholarships = Scholarship::all();
        }

        $studentCounter = 1;

        foreach ($constituentCampuses as $constituent) {
            // Get constituent + extensions
            $groupCampuses = Campus::where('id', $constituent->id)
                                    ->orWhere('parent_campus_id', $constituent->id)
                                    ->get();
            
            $totalInGroup = 30;
            $campusCount = $groupCampuses->count();
            
            // Distribute 30 students across these campuses
            // Simple logic: 30 / count. Remainder distributed.
            $baseCount = intval($totalInGroup / $campusCount);
            $remainder = $totalInGroup % $campusCount;

            foreach ($groupCampuses as $index => $campus) {
                $countForThisCampus = $baseCount + ($index < $remainder ? 1 : 0);
                
                for ($i = 0; $i < $countForThisCampus; $i++) {
                    
                    // Format: SR-00001
                    $srCode = sprintf("SR-%05d", $studentCounter);
                    $email = "{$srCode}@g.batstate-u.edu.ph";
                    $studentCounter++;

                    $firstName = $faker->firstName;
                    $lastName = $faker->lastName;
                    $gender = $faker->randomElement(['Male', 'Female']);

                    $student = User::create([
                        'name' => "{$firstName} {$lastName}",
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'middle_name' => $faker->lastName,
                        'email' => $email,
                        'password' => $password,
                        'role' => 'student',
                        'campus_id' => $campus->id,
                        'email_verified_at' => $now,
                        'sr_code' => $srCode,
                        'birthdate' => $now->copy()->subYears($faker->numberBetween(18, 24))->format('Y-m-d'),
                        'sex' => $gender,
                        'contact_number' => '09' . $faker->numberBetween(100000000, 999999999),
                        'program' => 'BS Information Technology',
                        'college' => 'CICS',
                        'year_level' => $faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                    ]);

                    // Create Student Profile
                    StudentProfile::create([
                        'user_id' => $student->id,
                        'street' => $faker->streetAddress,
                        'barangay' => $faker->word,
                        'town' => $faker->city,
                        'province' => $faker->state,
                        'zip_code' => $faker->postcode,
                        'gwa' => $faker->randomFloat(2, 1.0, 2.5),
                        'units_enrolled' => 21,
                        'father_name' => $faker->name('Male'),
                        'mother_name' => $faker->name('Female'),
                        'father_occupation' => $faker->jobTitle,
                        'mother_occupation' => $faker->jobTitle,
                        'annual_gross_income' => $faker->numberBetween(100000, 500000),
                    ]);

                    // Assign STATUS (Random)
                    // 0.00 - 0.15: Not Applied
                    // 0.15 - 0.30: In Progress
                    // 0.30 - 0.45: Pending
                    // 0.45 - 0.55: Rejected
                    // 0.55 - 0.75: Approved (SFAO Endorsed ONLY - Not yet a Scholar)
                    // 0.75 - 1.00: Scholar (Central Accepted - Has Scholar Record)
                    
                    $statusRoll = $faker->randomFloat(2, 0, 1);
                    
                    if ($statusRoll < 0.15) {
                        continue; // Not Applied
                    }

                    $scholarship = $scholarships->random();

                    // In Progress
                    if ($statusRoll < 0.30) {
                        Application::create([
                            'user_id' => $student->id,
                            'scholarship_id' => $scholarship->id,
                            'status' => 'in_progress',
                            'grant_count' => 0
                        ]);
                        continue;
                    }

                    // Pending
                    if ($statusRoll < 0.45) {
                        Application::create([
                            'user_id' => $student->id,
                            'scholarship_id' => $scholarship->id,
                            'status' => 'pending',
                            'grant_count' => 0
                        ]);
                        continue;
                    }

                    // Rejected
                    if ($statusRoll < 0.55) {
                         $app = Application::create([
                            'user_id' => $student->id,
                            'scholarship_id' => $scholarship->id,
                            'status' => 'rejected',
                            'remarks' => 'Did not meet requirements.',
                            'grant_count' => 0
                        ]);
                        
                        RejectedApplicant::create([
                            'user_id' => $student->id,
                            'scholarship_id' => $scholarship->id,
                            'application_id' => $app->id,
                            'rejected_by' => 'sfao',
                            'rejected_by_user_id' => 1,
                            'rejected_at' => $now,
                            'remarks' => 'Did not meet requirements.',
                        ]);
                        continue;
                    }

                    // Approved (Endorsed Only)
                    if ($statusRoll < 0.75) {
                        Application::create([
                            'user_id' => $student->id,
                            'scholarship_id' => $scholarship->id,
                            'status' => 'approved',
                            'grant_count' => 0 
                        ]);
                        continue;
                    }

                    // Scholar (Accepted)
                    // Application is Approved AND Scholar record exists
                    
                    $scholarType = $faker->randomElement(['new', 'old']);
                    $grantCount = $scholarType === 'new' ? 1 : $faker->numberBetween(2, 5);

                    $app = Application::create([
                        'user_id' => $student->id,
                        'scholarship_id' => $scholarship->id,
                        'status' => 'approved',
                        'grant_count' => 1 // Current application is always count 1 or just the latest
                    ]);

                    $scholarStatus = $faker->randomElement(['active', 'active', 'inactive', 'suspended', 'completed']);
                    
                    Scholar::create([
                        'user_id' => $student->id,
                        'scholarship_id' => $scholarship->id,
                        'application_id' => $app->id,
                        'status' => $scholarStatus,
                        'scholarship_start_date' => $scholarType === 'new' ? $now->copy()->subMonths(rand(1, 6)) : $now->copy()->subMonths(rand(12, 36)),
                        'scholarship_end_date' => $scholarStatus === 'completed' ? $now->copy()->subDays(rand(1, 30)) : null,
                        'type' => $scholarType,
                        'grant_count' => $grantCount,
                        'total_grant_received' => $grantCount * 5000.00, // Mock amount
                    ]);

                } // End Student Loop
            } // End Campus Loop
        } // End Constituent Loop
        
        $this->command->info("Seeding Completed. Created {$studentCounter} students.");
    }
}
