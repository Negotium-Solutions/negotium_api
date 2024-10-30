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
        Schema::create('profile_processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('profile_id')->nullable();
            $table->uuid('process_id')->nullable();
            $table->uuid('step_id')->nullable();
            $table->uuid('started_by_user_id')->nullable();
            $table->integer('process_status_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_processes');
    }
};
