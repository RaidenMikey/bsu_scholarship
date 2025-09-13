<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('scholarship_id'); // add this
            $table->string('form_137')->nullable();
            $table->string('grades')->nullable();
            $table->string('certificate')->nullable();
            $table->string('application_form')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('scholarship_id')->references('id')->on('scholarships')->onDelete('cascade');

            $table->unique(['user_id', 'scholarship_id']); // ensures one set of docs per scholarship
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_documents');
    }
}
