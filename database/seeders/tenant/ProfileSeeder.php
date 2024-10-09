<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Session;

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
            'profile_type_id' => 1,
            'avatar' => '/images/individual/avatar'.rand(1, 5).'.png',
            'schema_id' => $individual->id
        ])->create();

        Profile::factory([
            'profile_type_id' => 2,
            'avatar' => '/images/business/avatar'.rand(1, 5).'.png',
            'schema_id' => $business->id
        ])->create();

        $phoneNumbers = ["0614116444", "0848791089","0642596255"];
        Profile::factory(20)->create();

        $profiles = Profile::get();
        foreach ($profiles as $profile) {
            $tableName = '';
            $dynamicModel = new DynamicModel();


            if ($profile->profile_type_id == 1) {
                $tableName = $individual->name;
                Session::put('table_name', $tableName);
                $dynamicModel->first_name = fake()->firstName();
                $dynamicModel->last_name = fake()->lastName();
                $dynamicModel->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $dynamicModel->email = fake()->email();
                // Update profile
                $profile->schema_id = $individual->id;
                $profile->save();
            }
            if ($profile->profile_type_id == 2) {
                $tableName = $business->name;
                Session::put('table_name', $tableName);
                $dynamicModel->company_name = fake()->company();
                $dynamicModel->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $dynamicModel->email = fake()->email();
                // Update profile
                $profile->schema_id = $business->id;
                $profile->save();
            }

            $dynamicModel->parent_id = $profile->id;
            if($dynamicModel->get()->count() === 0){
                if($profile->profile_type_id == 1) {
                    $dynamicModel->first_name = 'Nico';
                    $dynamicModel->last_name = 'Van Der Meulen';
                    $dynamicModel->email = 'nico@negotium-solutions.com';
                    $dynamicModel->cell_number = '0832848212';
                } else {
                    $dynamicModel->company_name = 'Negotium Solutions';
                    $dynamicModel->email = 'admin@negotium-solutions.com';
                    $dynamicModel->cell_number = '0614116444';
                }
            }
            $dynamicModel->save();
        }
    }
}
