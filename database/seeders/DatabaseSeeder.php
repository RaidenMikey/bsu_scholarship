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
        // Call branch seeder first so branch_id exists before users
        $this->call([
            BranchSeeder::class,
            UsersTableSeeder::class,
            FormsTableSeeder::class,
            ScholarshipsTableSeeder::class,
        ]);
    }
}
