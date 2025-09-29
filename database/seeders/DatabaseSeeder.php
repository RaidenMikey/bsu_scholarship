<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Call campus seeder first so campus_id exists before users
        $this->call([
            CampusSeeder::class,
            UsersTableSeeder::class,
            FormsTableSeeder::class,
            ScholarshipsTableSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
