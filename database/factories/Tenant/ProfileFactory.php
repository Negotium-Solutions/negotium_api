<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Schema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profileType = rand(1, 2);
        $individual = Schema::where('name', 'individual_1')->first();
        $business = Schema::where('name', 'business_2')->first();

        if($profileType === 1) {
            $factoryData = [
                'profile_type_id' => $profileType,
                'avatar' => '/images/individual/avatar'.rand(1, 5).'.png',
                'schema_id' => $individual
            ];
        }

        if($profileType === 2) {
            $factoryData = [
                'profile_type_id' => $profileType,
                'avatar' => '/images/business/avatar'.rand(1, 5).'.png',
                'schema_id' => $business
            ];
        }

        return $factoryData;
    }
}
