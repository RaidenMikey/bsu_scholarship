<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run()
    {
        DB::table('branches')->insert([
            ['name' => 'ARASOF', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Balayan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pablo Borbon', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
