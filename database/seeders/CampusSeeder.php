<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data - handle foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('campuses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert constituent campuses first
        $pabloBorbon = DB::table('campuses')->insertGetId([
            'name' => 'BatStateU Pablo Borbon (Main)',
            'type' => 'constituent',
            'parent_campus_id' => null,
            'has_sfao_admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $alangilan = DB::table('campuses')->insertGetId([
            'name' => 'BatStateU Alangilan',
            'type' => 'constituent',
            'parent_campus_id' => null,
            'has_sfao_admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $arasof = DB::table('campuses')->insertGetId([
            'name' => 'BatStateU ARASOF–Nasugbu (Apolinario R. Apacible School of Fisheries)',
            'type' => 'constituent',
            'parent_campus_id' => null,
            'has_sfao_admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $jplpc = DB::table('campuses')->insertGetId([
            'name' => 'BatStateU JPLPC–Malvar (Jose P. Laurel Polytechnic College)',
            'type' => 'constituent',
            'parent_campus_id' => null,
            'has_sfao_admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $lipa = DB::table('campuses')->insertGetId([
            'name' => 'BatStateU Lipa',
            'type' => 'constituent',
            'parent_campus_id' => null,
            'has_sfao_admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert extension campuses
        DB::table('campuses')->insert([
            // Pablo Borbon extensions
            ['name' => 'BatStateU San Juan', 'type' => 'extension', 'parent_campus_id' => $pabloBorbon, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BatStateU Mabini', 'type' => 'extension', 'parent_campus_id' => $pabloBorbon, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // ARASOF extensions
            ['name' => 'BatStateU Balayan', 'type' => 'extension', 'parent_campus_id' => $arasof, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BatStateU Lemery', 'type' => 'extension', 'parent_campus_id' => $arasof, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // JPLPC extensions
            ['name' => 'BatStateU Rosario', 'type' => 'extension', 'parent_campus_id' => $jplpc, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
            
            // Lipa extensions
            ['name' => 'BatStateU Lobo', 'type' => 'extension', 'parent_campus_id' => $lipa, 'has_sfao_admin' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
