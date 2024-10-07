<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schemas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('table_name')->nullable();
            $table->integer('dynamic_model_category_id')->nullable(); // Profiles(Individual, Business), Processes(Category 01, Category 02, Category 03, ...)
            $table->integer('dynamic_model_type_id')->nullable(); // Process / Profile
            $table->enum('quick_capture', ['Yes', 'No'])->default('No');
            $table->enum('status', ['Active', 'Draft'])->default('Draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schemas');
    }
};
