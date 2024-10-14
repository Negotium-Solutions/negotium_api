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
        $individualSchema = new Schema();
        $individualSchema->createDynamicModel('Individual', 1, 1, 1, 'Yes');
        $data = json_decode(file_get_contents(base_path('database/templates/profile/personal_information.json')));
        $individualSchema->createDynamicModelFields($individualSchema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/personal_contact_information.json')));
        $individualSchema->createDynamicModelFields($individualSchema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/home_address.json')));
        $individualSchema->createDynamicModelFields($individualSchema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/work_address.json')));
        $individualSchema->createDynamicModelFields($individualSchema, $data, true);

        $businessSchema = new Schema();
        $businessSchema->createDynamicModel('Business', 2, 1, 2, 'Yes');
        $data = json_decode(file_get_contents(base_path('database/templates/profile/company_information.json')));
        $businessSchema->createDynamicModelFields($businessSchema, $data, true);

        $data = json_decode(file_get_contents(base_path('database/templates/profile/business_contact_information.json')));
        $businessSchema->createDynamicModelFields($businessSchema, $data, true);

        $phoneNumbers = ["0614116444", "0848791089","0642596255"];
        /*--------------------- Seed Profile Data - Start ------------------------*/
        for ($i = 0; $i < 20; $i++) {
            $dynamic_model_category_id = rand(1, 2);

            Session::put('table_name', $individualSchema->table_name);
            $profile = new DynamicModel();
            if ($dynamic_model_category_id === 1) {
                $profile->schema_id = $individualSchema->id;
                $profile->parent_id = 1;
                $profile->avatar = '/images/individual/avatar'.rand(1, 5).'.png';
                $profile->first_name = fake()->firstName();
                $profile->last_name = fake()->lastName();
                $profile->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $profile->email = fake()->email();
                $profile->save();
            }

            Session::put('table_name', $businessSchema->table_name);
            $profile = new DynamicModel();
            if ($dynamic_model_category_id === 2) {
                $profile->schema_id = $businessSchema->id;
                $profile->parent_id = 2;
                $profile->avatar = '/images/business/avatar'.rand(1, 5).'.png';
                $profile->company_name = fake()->company();
                $profile->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $profile->email = fake()->email();
                $profile->save();
            }
        }
        /*--------------------- Seed Profile Data - End --------------------------*/
    }
}
