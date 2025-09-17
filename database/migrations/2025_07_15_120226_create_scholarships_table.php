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
            $table->text('description');
            $table->date('deadline');
            $table->integer('slots_available')->nullable();
            $table->decimal('grant_amount', 10, 2)->nullable(); // ₱99999999.99 max
            $table->boolean('renewal_allowed')->default(false); // whether renewal is allowed
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
