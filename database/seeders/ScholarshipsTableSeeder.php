<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use Carbon\Carbon;

class ScholarshipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scholarships = [
            [
                'scholarship_name' => 'Academic Excellence Scholarship',
                'description'      => 'Awarded to students with excellent academic performance.',
                'deadline'         => Carbon::now()->addMonths(2),
                'slots_available'  => 50,
                'grant_amount'     => 10000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '1.50', 'is_mandatory' => true],
                    ['name' => 'year_level', 'value' => 'Second Year', 'is_mandatory' => true],
                    ['name' => 'program', 'value' => 'BS Computer Science', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'Athletic Scholarship',
                'description'      => 'For students excelling in sports and athletics.',
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => 30,
                'grant_amount'     => 8000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '2.50', 'is_mandatory' => true],
                    ['name' => 'age', 'value' => '18', 'is_mandatory' => true],
                ],
            ],
            [
                'scholarship_name' => 'Leadership Grant',
                'description'      => 'For students who have demonstrated strong leadership skills.',
                'deadline'         => Carbon::now()->addMonths(3),
                'slots_available'  => 20,
                'grant_amount'     => 7000,
                'renewal_allowed'  => false,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '2.00', 'is_mandatory' => true],
                    ['name' => 'income', 'value' => '15000', 'is_mandatory' => true],
                    ['name' => 'year_level', 'value' => 'Third Year', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'Cultural Arts Scholarship',
                'description'      => 'Supports students active in cultural and performing arts.',
                'deadline'         => Carbon::now()->addWeeks(6),
                'slots_available'  => 15,
                'grant_amount'     => 6000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '2.75', 'is_mandatory' => true],
                    ['name' => 'program', 'value' => 'BS Computer Science', 'is_mandatory' => true],
                    ['name' => 'campus', 'value' => 'BatStateU Alangilan', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'Financial Assistance Grant',
                'description'      => 'Aimed to help financially challenged students continue their studies.',
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => null, // unlimited
                'grant_amount'     => 5000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'income', 'value' => '10000', 'is_mandatory' => true],
                    ['name' => 'disability', 'value' => 'no', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'STEM Excellence Scholarship',
                'description'      => 'For students pursuing STEM programs with outstanding performance.',
                'deadline'         => Carbon::now()->addMonths(2),
                'slots_available'  => 25,
                'grant_amount'     => 12000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '1.75', 'is_mandatory' => true],
                    ['name' => 'program', 'value' => 'BS Computer Science', 'is_mandatory' => true],
                    ['name' => 'year_level', 'value' => 'Second Year', 'is_mandatory' => true],
                    ['name' => 'sex', 'value' => 'female', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'Community Service Grant',
                'description'      => 'For students actively involved in community service and volunteer work.',
                'deadline'         => Carbon::now()->addWeeks(8),
                'slots_available'  => 18,
                'grant_amount'     => 5500,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '2.25', 'is_mandatory' => true],
                    ['name' => 'year_level', 'value' => 'Third Year', 'is_mandatory' => true],
                    ['name' => 'campus', 'value' => 'BatStateU Main', 'is_mandatory' => false],
                ],
            ],
            [
                'scholarship_name' => 'First Generation Scholar',
                'description'      => 'Supporting first-generation college students in their academic journey.',
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => 40,
                'grant_amount'     => 4500,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'conditions' => [
                    ['name' => 'gwa', 'value' => '2.50', 'is_mandatory' => true],
                    ['name' => 'year_level', 'value' => 'First Year', 'is_mandatory' => true],
                    ['name' => 'income', 'value' => '12000', 'is_mandatory' => true],
                ],
            ],
        ];

        foreach ($scholarships as $data) {
            $conditions = $data['conditions'] ?? [];
            unset($data['conditions']); // Remove conditions from scholarship data
            
            $scholarship = Scholarship::create($data);
            
            // Add multiple conditions for each scholarship
            foreach ($conditions as $condition) {
                ScholarshipRequirement::create([
                    'scholarship_id' => $scholarship->id,
                    'type' => 'condition',
                    'name' => $condition['name'],
                    'value' => $condition['value'],
                    'is_mandatory' => $condition['is_mandatory'],
                ]);
            }
        }
    }
}
