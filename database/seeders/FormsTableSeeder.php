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
                'civil_status' => fake()->randomElement(['single', 'married']),
                'disability' => null,
                'tribe' => null,
                'citizenship' => 'Filipino',
                'birthdate' => fake()->date(),
                'birthplace' => fake()->city,
                'birth_order' => fake()->randomElement(['1st', '2nd', '3rd']),
                'email' => $user->email, // link with the user's email
                'telephone' => fake()->phoneNumber,
                'religion' => fake()->randomElement(['Catholic', 'Christian', 'Muslim']),
                'highschool_type' => fake()->randomElement(['Public', 'Private']),
                'monthly_allowance' => fake()->numberBetween(1000, 10000),
                'living_arrangement' => fake()->randomElement(['With Parents', 'Dorm', 'Apartment']),
                'transportation' => fake()->randomElement(['Jeep', 'Bus', 'Tricycle', 'Walking']),
                'education_level' => 'College',
                'program' => 'BS Computer Science',
                'college' => 'CICS',
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'campus' => 'BatStateU Alangilan',
                'gwa' => (function () {
                    $values = ['1.00', '1.25', '1.50', '1.75',
                            '2.00', '2.25', '2.50', '2.75',
                            '3.00', '5.00', 'INC'];

                    $selected = fake()->randomElement($values);

                    return $selected === 'INC' ? null : $selected;
                })(),
                'honors' => fake()->optional()->randomElement(['Dean’s Lister', 'Honor Student']),
                'units_enrolled' => fake()->numberBetween(18, 30),
                'academic_year' => '2024-2025',
                'has_existing_scholarship' => fake()->boolean,
                'existing_scholarship_details' => null,
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
