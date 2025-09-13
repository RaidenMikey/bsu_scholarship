<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scholarship;
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
                'minimum_gwa'      => 1.50,
                'deadline'         => Carbon::now()->addMonths(2),
                'slots_available'  => 50,
                'grant_amount'     => 10000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
            ],
            [
                'scholarship_name' => 'Athletic Scholarship',
                'description'      => 'For students excelling in sports and athletics.',
                'minimum_gwa'      => 2.50,
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => 30,
                'grant_amount'     => 8000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
            ],
            [
                'scholarship_name' => 'Leadership Grant',
                'description'      => 'For students who have demonstrated strong leadership skills.',
                'minimum_gwa'      => 2.00,
                'deadline'         => Carbon::now()->addMonths(3),
                'slots_available'  => 20,
                'grant_amount'     => 7000,
                'renewal_allowed'  => false,
                'created_by'       => 1,
            ],
            [
                'scholarship_name' => 'Cultural Arts Scholarship',
                'description'      => 'Supports students active in cultural and performing arts.',
                'minimum_gwa'      => 2.75,
                'deadline'         => Carbon::now()->addWeeks(6),
                'slots_available'  => 15,
                'grant_amount'     => 6000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
            ],
            [
                'scholarship_name' => 'Financial Assistance Grant',
                'description'      => 'Aimed to help financially challenged students continue their studies.',
                'minimum_gwa'      => null, // INC = null
                'deadline'         => Carbon::now()->addMonths(1),
                'slots_available'  => null, // unlimited
                'grant_amount'     => 5000,
                'renewal_allowed'  => true,
                'created_by'       => 1,
            ],
        ];

        foreach ($scholarships as $data) {
            Scholarship::create($data);
        }
    }
}
