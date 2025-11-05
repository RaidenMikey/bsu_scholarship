<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Call campus seeder first so campus_id exists before users
        $this->call([
            CampusSeeder::class,
            UsersTableSeeder::class,
            ScholarshipsTableSeeder::class,
            FormsTableSeeder::class,
            NotificationSeeder::class,
            ApplicationsAndScholarsSeeder::class,
        ]);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
