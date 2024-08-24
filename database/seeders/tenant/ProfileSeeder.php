<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Profile;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $individual = Schema::where('name', 'individual_1')->first();
        $business = Schema::where('name', 'business_2')->first();

        Profile::factory([
            'first_name' => 'Nico',
            'last_name' => 'Van Der Meulen',
            'profile_type_id' => 1,
            'avatar' => '/images/individual/avatar'.rand(1, 5).'.png',
            'schema_id' => $individual->id
        ])->create();

        Profile::factory([
            'company_name' => 'Negotium Solutions',
            'profile_type_id' => 2,
            'avatar' => '/images/business/avatar'.rand(1, 5).'.png',
            'schema_id' => $business->id
        ])->create();

        Profile::factory(20)->create();
    }
}
