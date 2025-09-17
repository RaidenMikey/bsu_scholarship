<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Form;
use Illuminate\Support\Facades\DB;

class FormsTableSeeder extends Seeder
{
    public function run()
    {
        // clear old data
        DB::table('forms')->truncate();

        // loop through all users
        $users = User::all();

        foreach ($users as $user) {
            Form::create([
                'user_id' => $user->id,
                'last_name' => fake()->lastName,
                'first_name' => fake()->firstName,
                'middle_name' => fake()->optional()->lastName,
                // Address breakdown
                'street_barangay' => fake()->streetAddress,
                'town_city'       => fake()->city,
                'province'        => 'Batangas', // or fake()->state if you want random
                'zip_code'        => fake()->postcode,
                'age' => fake()->numberBetween(18, 30),
                'sex' => fake()->randomElement(['male', 'female']),
                'civil_status' => fake()->randomElement(['Single', 'Married', 'Widowed', 'Divorced', 'Separated']),
                'disability' => fake()->optional(0.1)->randomElement(['Visual Impairment', 'Hearing Impairment', 'Mobility Impairment', 'Learning Disability']),
                'tribe' => fake()->optional(0.2)->randomElement(['Tagalog', 'Bisaya', 'Ilocano', 'Bicolano', 'Waray']),
                'citizenship' => 'Filipino',
                'birthdate' => fake()->date('Y-m-d', '2000-01-01'),
                'birthplace' => fake()->city,
                'birth_order' => fake()->randomElement(['First Born', 'Middle Born', 'Last Born']),
                'email' => $user->email, // link with the user's email
                'telephone' => fake()->phoneNumber,
                'religion' => fake()->randomElement(['Catholic', 'Christian', 'Muslim', 'Buddhist', 'Hindu', 'Agnostic']),
                'highschool_type' => fake()->randomElement(['Public', 'Private']),
                'monthly_allowance' => fake()->numberBetween(1000, 20000),
                'living_arrangement' => fake()->randomElement(['Living with Parents', 'Living with Relatives', 'Owned House', 'Boarding House', 'Apartment']),
                'living_arrangement_other' => null,
                'transportation' => fake()->randomElement(['Public Transportation', 'Own Vehicle', 'School Service']),
                'transportation_other' => null,
                'education_level' => fake()->randomElement(['Undergraduate', 'Graduate School', 'Integrated / Laboratory School']),
                'program' => fake()->randomElement([
                    'BS Computer Science', 'BS Information Technology', 'BS Computer Engineering', 
                    'BS Electronics Engineering', 'BS Civil Engineering', 'BS Mechanical Engineering',
                    'BS Electrical Engineering', 'BS Industrial Engineering', 'BS Accountancy',
                    'BS Business Administration', 'BS Tourism Management', 'BS Hospitality Management',
                    'BS Psychology', 'BS Education', 'BS Nursing', 'BS Medical Technology',
                    'BS Pharmacy', 'BS Biology', 'BS Chemistry', 'BS Mathematics', 'BS Physics'
                ]),
                'college' => fake()->randomElement(['CICS', 'CTE', 'CABEIHM', 'CAS']),
                'year_level' => fake()->randomElement(['First Year', 'Second Year', 'Third Year', 'Fourth Year']),
                'campus' => fake()->randomElement([
                    'BatStateU Alangilan', 'BatStateU Main', 'BatStateU Lipa', 'BatStateU Malvar',
                    'BatStateU Lemery', 'BatStateU San Juan', 'BatStateU Lobo', 'BatStateU Rosario',
                    'BatStateU Balayan', 'BatStateU Calaca', 'BatStateU Calatagan', 'BatStateU Mabini',
                    'BatStateU Nasugbu', 'BatStateU Tuy'
                ]),
                'gwa' => fake()->randomFloat(2, 1.00, 3.00),
                'honors' => fake()->optional(0.3)->randomElement(['Dean\'s Lister', 'Honor Student', 'Summa Cum Laude', 'Magna Cum Laude', 'Cum Laude']),
                'units_enrolled' => fake()->numberBetween(18, 30),
                'academic_year' => '2024-2025',
                'has_existing_scholarship' => fake()->boolean(20), // 20% chance of having existing scholarship
                'existing_scholarship_details' => fake()->optional(0.2)->randomElement([
                    'CHED Scholarship', 'DOST Scholarship', 'Local Government Scholarship',
                    'Private Foundation Grant', 'University Financial Aid'
                ]),
                'father_living' => true,
                'father_name' => fake()->name('male'),
                'father_age' => fake()->numberBetween(40, 60),
                'father_residence' => fake()->city,
                'father_education' => 'College Graduate',
                'father_contact' => fake()->phoneNumber,
                'father_occupation' => fake()->jobTitle,
                'father_company' => fake()->company,
                'father_company_address' => fake()->address,
                'father_employment_status' => 'Employed',
                'mother_living' => true,
                'mother_name' => fake()->name('female'),
                'mother_age' => fake()->numberBetween(40, 60),
                'mother_residence' => fake()->city,
                'mother_education' => 'College Graduate',
                'mother_contact' => fake()->phoneNumber,
                'mother_occupation' => fake()->jobTitle,
                'mother_company' => fake()->company,
                'mother_company_address' => fake()->address,
                'mother_employment_status' => 'Employed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
