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
        Schema::create('base_apis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('route')->nullable();
            $table->longText('payload')->nullable();
            $table->longText('response')->nullable();
            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();
            $table->string('message')->nullable();
            $table->integer('code')->nullable();
            $table->enum('is_error', ['success', 'error'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('base_apis');
    }
};
