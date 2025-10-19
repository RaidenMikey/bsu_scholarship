<?php
// database/migrations/xxxx_xx_xx_create_scholarships_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('scholarship_name');
            $table->enum('scholarship_type', ['internal', 'external', 'public', 'government'])->default('internal');
            $table->text('description');
            $table->date('submission_deadline'); // Changed from deadline
            $table->date('application_start_date')->nullable(); // When applications can start
            $table->integer('slots_available')->nullable();
            $table->decimal('grant_amount', 10, 2)->nullable(); // ₱99999999.99 max
            $table->boolean('renewal_allowed')->default(false); // whether renewal is allowed
            $table->enum('grant_type', ['one_time', 'recurring', 'discontinued'])->default('recurring'); // Grant distribution type
            $table->boolean('is_active')->default(true);
            $table->enum('priority_level', ['high', 'medium', 'low'])->default('medium'); // Scholarship priority
            $table->text('eligibility_notes')->nullable(); // Additional eligibility information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
