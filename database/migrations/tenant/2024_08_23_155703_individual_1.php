<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant\DynamicModelField;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('individual_1', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_number')->nullable();
            $table->string('maiden_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('work_number')->nullable();
            $table->string('home_number')->nullable();
            $table->string('resident_type')->nullable();
            $table->string('home_building_name')->nullable();
            $table->string('home_unit_number')->nullable();
            $table->string('home_street_name')->nullable();
            $table->string('home_suburb')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_country')->nullable();
            $table->string('home_postal_code')->nullable();
            $table->string('work_building_name')->nullable();
            $table->string('work_street_address')->nullable();
            $table->string('work_suburb')->nullable();
            $table->string('work_city')->nullable();
            $table->string('work_country')->nullable();
            $table->string('work_postal_code')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_1');
    }
};
