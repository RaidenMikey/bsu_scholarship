<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('not_applied', 'in_progress', 'pending', 'approved', 'rejected', 'claimed') DEFAULT 'not_applied'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM('not_applied', 'in_progress', 'pending', 'approved', 'rejected') DEFAULT 'not_applied'");
    }
};
