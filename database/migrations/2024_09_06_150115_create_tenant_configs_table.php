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
        Schema::create('tenant_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('tenant_id')->nullable();
            $table->string('infobip_api_key')->nullable();
            $table->string('infobip_base_url')->nullable();
            $table->string('infobip_phone_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_configs');
    }
};
