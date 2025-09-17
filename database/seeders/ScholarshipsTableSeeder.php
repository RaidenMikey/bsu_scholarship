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
                'gwa_requirement'  => 1.50,
            ],
            [
                'scholarship_name' => 'Athletic Scholarship',
                'description'      => 'For students excelling in sports and athletics.',
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => 30,
                'grant_amount'     => 8000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'gwa_requirement'  => 2.50,
            ],
            [
                'scholarship_name' => 'Leadership Grant',
                'description'      => 'For students who have demonstrated strong leadership skills.',
                'deadline'         => Carbon::now()->addMonths(3),
                'slots_available'  => 20,
                'grant_amount'     => 7000,
                'renewal_allowed'  => false,
                'created_by'       => 1,
                'gwa_requirement'  => 2.00,
            ],
            [
                'scholarship_name' => 'Cultural Arts Scholarship',
                'description'      => 'Supports students active in cultural and performing arts.',
                'deadline'         => Carbon::now()->addWeeks(6),
                'slots_available'  => 15,
                'grant_amount'     => 6000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'gwa_requirement'  => 2.75,
            ],
            [
                'scholarship_name' => 'Financial Assistance Grant',
                'description'      => 'Aimed to help financially challenged students continue their studies.',
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => null, // unlimited
                'grant_amount'     => 5000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
                'gwa_requirement'  => null, // No GWA requirement
            ],
        ];

        foreach ($scholarships as $data) {
            $gwaRequirement = $data['gwa_requirement'];
            unset($data['gwa_requirement']); // Remove from scholarship data
            
            $scholarship = Scholarship::create($data);
            
            // Add GWA requirement as a condition if specified
            if ($gwaRequirement !== null) {
                ScholarshipRequirement::create([
                    'scholarship_id' => $scholarship->id,
                    'type' => 'condition',
                    'name' => 'gwa',
                    'value' => $gwaRequirement,
                    'is_mandatory' => true,
                ]);
            }
        }
    }
}
