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
        Schema::create('communications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject')->nullable();
            $table->longText('note')->nullable();
            $table->uuid('user_id')->nullable();
            $table->integer('profile_id')->nullable();
            $table->integer('communication_type_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
