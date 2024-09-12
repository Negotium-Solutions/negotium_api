<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
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
        $individual = Schema::where('name', 'like', '%individual%')->first();
        $business = Schema::where('name', 'like', '%business%')->first();

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

        $profiles = Profile::get();
        foreach ($profiles as $profile) {
            $tableName = $individual->name;
            if ($profile->profile_type_id == 2) {
                $tableName = $business->name;
            }

            $dynamicModel = new DynamicModel();
            $dynamicModel->setTable($tableName);
            $dynamicModel->parent_id = $profile->id;
            $dynamicModel->save();
        }
    }
}
