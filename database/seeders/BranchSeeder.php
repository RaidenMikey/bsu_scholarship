<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run()
    {
        DB::table('branches')->insert([
            ['name' => 'Pablo Borbon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alangilan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ARASOF-Nasugbu', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JPLPC-Malvar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lipa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'San Juan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mabini', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Balayan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lemery', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rosario', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lobo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
