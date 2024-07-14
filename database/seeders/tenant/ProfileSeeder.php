<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profile::factory([
            'first_name' => 'Nico',
            'last_name' => 'Vermeulen',
            'profile_type_id' => 1
        ])->create();

        Profile::factory([
            'company_name' => 'Negotium Solutions',
            'profile_type_id' => 2
        ])->create();

        Profile::factory(20)->create();
    }
}
