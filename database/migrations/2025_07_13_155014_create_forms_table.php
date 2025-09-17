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

            // Foreign Key to users
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // ------------------- Personal Data -------------------
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();

            // Address broken down
            $table->string('street_barangay')->nullable();
            $table->string('town_city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();

            $table->integer('age')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('civil_status')->nullable();
            $table->string('disability')->nullable();
            $table->string('tribe')->nullable();
            $table->string('citizenship')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('birth_order')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('religion')->nullable();
            $table->enum('highschool_type', ['Public', 'Private'])->nullable();

            $table->decimal('monthly_allowance', 10, 2)->nullable();
            $table->string('living_arrangement')->nullable();
            $table->string('living_arrangement_other')->nullable();
            $table->string('transportation')->nullable();
            $table->string('transportation_other')->nullable();

            // ------------------- Academic Data -------------------
            $table->string('education_level')->nullable();
            $table->string('program')->nullable();
            $table->string('college')->nullable();
            $table->string('year_level')->nullable();
            $table->string('campus')->nullable();
            $table->decimal('gwa', 4, 2)->nullable();
            $table->string('honors')->nullable();
            $table->integer('units_enrolled')->nullable();
            $table->string('academic_year')->nullable();
            $table->boolean('has_existing_scholarship')->default(false);
            $table->text('existing_scholarship_details')->nullable();

            // ------------------- Family Data -------------------
            // Father
            $table->boolean('father_living')->nullable();
            $table->string('father_name')->nullable();
            $table->integer('father_age')->nullable();
            $table->string('father_residence')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_company')->nullable();
            $table->string('father_company_address')->nullable();
            $table->string('father_employment_status')->nullable();

            // Mother
            $table->boolean('mother_living')->nullable();
            $table->string('mother_name')->nullable();
            $table->integer('mother_age')->nullable();
            $table->string('mother_residence')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_company')->nullable();
            $table->string('mother_company_address')->nullable();
            $table->string('mother_employment_status')->nullable();

            // Additional Family Info
            $table->integer('family_members_count')->nullable();
            $table->integer('siblings_count')->nullable();
            $table->string('family_form')->nullable(); 
            // e.g. living together, separated, annulled, etc.

            // ------------------- Income -------------------
            $table->string('monthly_family_income_bracket')->nullable();
            // e.g. "<10957", "10957-21194", etc.

            $table->text('other_income_sources')->nullable(); 
            // could store JSON like [{"source":"Relatives","amount":5000},{"source":"Government","amount":2000}]

            // ------------------- House Profile & Utilities -------------------
            $table->text('vehicle_ownership')->nullable(); 
            // e.g. "Car - 1, Motorcycle - 2"

            $table->text('appliances')->nullable(); 
            // e.g. "TV - 2, Refrigerator - 1"

            $table->string('house_ownership')->nullable(); 
            // e.g. rented, owned, government, etc.

            $table->string('house_material')->nullable(); 
            // e.g. wood, bamboo, half-concrete, etc.

            $table->string('house_type')->nullable(); 
            // e.g. single, duplex, multi-unit

            $table->string('cooking_utilities')->nullable(); 
            // e.g. LPG, wood, kerosene

            $table->string('water_source')->nullable(); 
            $table->string('electricity_source')->nullable();

            $table->decimal('monthly_bills_electric', 10, 2)->nullable();
            $table->decimal('monthly_bills_telephone', 10, 2)->nullable();
            $table->decimal('monthly_bills_internet', 10, 2)->nullable();

            // ------------------- Certification -------------------
            $table->string('student_signature')->nullable(); 
            // optional, could be image path if digital

            $table->date('date_signed')->nullable();

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
