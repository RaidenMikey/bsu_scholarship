<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update existing records from 'public' to 'private'
        DB::table('scholarships')
            ->where('scholarship_type', 'public')
            ->update(['scholarship_type' => 'private']);

        // Modify the enum column to change 'public' to 'private'
        DB::statement("ALTER TABLE scholarships MODIFY COLUMN scholarship_type ENUM('internal', 'external', 'private', 'government') DEFAULT 'internal'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Update existing records from 'private' back to 'public'
        DB::table('scholarships')
            ->where('scholarship_type', 'private')
            ->update(['scholarship_type' => 'public']);

        // Revert the enum column back to 'public'
        DB::statement("ALTER TABLE scholarships MODIFY COLUMN scholarship_type ENUM('internal', 'external', 'public', 'government') DEFAULT 'internal'");
    }
};
