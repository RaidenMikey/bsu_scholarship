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
        Schema::table('reports', function (Blueprint $table) {
            $table->string('student_type')->nullable()->after('report_type');
            $table->unsignedBigInteger('college_id')->nullable()->after('campus_id');
            $table->unsignedBigInteger('program_id')->nullable()->after('college_id');
            $table->unsignedBigInteger('track_id')->nullable()->after('program_id');
            $table->string('academic_year')->nullable()->after('track_id');

            // Foreign Keys (optional but good for integrity if tables exist)
            // Assuming referenced tables are 'colleges', 'programs', 'program_tracks'
            // We'll add them but make them nullable.
            // Note: If data already exists, these might fail if IDs don't match. But for now new cols are null.
            // Using constrained() shortcut might be safer if we are sure of table names.
            // Given I checked them: College -> colleges, Program -> programs, ProgramTrack -> program_tracks
            
            // $table->foreign('college_id')->references('id')->on('colleges')->nullOnDelete();
            // $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
            // $table->foreign('track_id')->references('id')->on('program_tracks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['student_type', 'college_id', 'program_id', 'track_id', 'academic_year']);
        });
    }
};
