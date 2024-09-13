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
            $tableName = '';
            $dynamicModel = new DynamicModel();
            if ($profile->profile_type_id == 1) {
                $tableName = $individual->name;
                $dynamicModel->first_name_1 = fake()->firstName();
                $dynamicModel->last_name_2 = fake()->lastName();
                $dynamicModel->cell_number_3 = fake()->phoneNumber();
                $dynamicModel->email_4 = fake()->email();
                // Update profile
                $profile->schema_id = $individual->id;
                $profile->save();
            }
            if ($profile->profile_type_id == 2) {
                $tableName = $business->name;
                $dynamicModel->company_name_25 = fake()->company();
                $dynamicModel->cell_number_27 = fake()->phoneNumber();
                $dynamicModel->email_28 = fake()->email();
                // Update profile
                $profile->schema_id = $business->id;
                $profile->save();
            }
            $dynamicModel->setTable($tableName);
            $dynamicModel->parent_id = $profile->id;
            $dynamicModel->save();
        }
    }
}
