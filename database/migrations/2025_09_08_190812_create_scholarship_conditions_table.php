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
        Schema::create('scholarship_conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_id');
            $table->string('field_name'); // e.g. 'gwa', 'income', 'disability', 'year_level'
            $table->string('value'); // e.g. 2.50, Yes, 10000, 2

            $table->timestamps();

            $table->foreign('scholarship_id')
                ->references('id')->on('scholarships')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scholarship_conditions');
    }
};
