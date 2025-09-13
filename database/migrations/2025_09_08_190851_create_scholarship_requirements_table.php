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
        Schema::create('scholarship_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_id');
            $table->string('name'); // e.g. 'Barangay Clearance', 'Birth Certificate'
            $table->text('description')->nullable(); // optional details
            $table->boolean('is_mandatory')->default(true);

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
        Schema::dropIfExists('scholarship_requirements');
    }
};
