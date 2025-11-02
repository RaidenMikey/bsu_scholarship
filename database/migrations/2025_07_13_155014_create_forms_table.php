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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            // Link to users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // ------------------- PERSONAL DATA -------------------
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();

            $table->integer('age')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('civil_status')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();

            // Address broken down
            $table->string('street_barangay')->nullable();
            $table->string('town_city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();

            $table->string('citizenship')->nullable();
            $table->string('disability')->nullable(); // Type of disability
            $table->string('tribe')->nullable(); // Tribal membership

            // ------------------- ACADEMIC DATA -------------------
            $table->string('sr_code')->nullable();
            $table->enum('education_level', ['Undergraduate', 'Graduate School', 'Integrated School'])->nullable();
            $table->string('program')->nullable();
            $table->string('college_department')->nullable();
            $table->string('year_level')->nullable();
            $table->string('campus')->nullable();
            $table->decimal('previous_gwa', 4, 2)->nullable();
            $table->string('honors_received')->nullable();
            $table->integer('units_enrolled')->nullable();
            $table->string('scholarship_applied')->nullable();
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->boolean('has_existing_scholarship')->default(false);
            $table->text('existing_scholarship_details')->nullable();

            // ------------------- FAMILY DATA -------------------
            $table->enum('father_status', ['living', 'deceased'])->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_address')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_income_bracket')->nullable();

            $table->enum('mother_status', ['living', 'deceased'])->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_address')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_income_bracket')->nullable();

            $table->integer('siblings_count')->nullable();

            // ------------------- ESSAY / QUESTION -------------------
            $table->text('reason_for_applying')->nullable();

            // ------------------- CERTIFICATION -------------------
            $table->string('student_signature')->nullable();
            $table->date('date_signed')->nullable();

            // ------------------- STATUS / META -------------------
            $table->enum('form_status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('draft');
            $table->text('reviewer_remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
};
