<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Session;

class DynamicModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*-----------------------------Seed Profile - Start------------------------------------------*/
        $individualSchema = Schema::where('dynamic_model_category_id', 1)->first();
        $businessSchema = Schema::where('dynamic_model_category_id', 2)->first();

        for ($i = 0; $i < 20; $i++) {
            $dynamic_model_category_id = rand(1, 2);
            $tableName = $individualSchema->table_name;
            if ($dynamic_model_category_id === 2) {
                $tableName = $businessSchema->table_name;
            }

            Session::put('table_name', $tableName);
            $profile = new DynamicModel();

            $phoneNumbers = ["0614116444", "0848791089","0642596255"];
            if ($dynamic_model_category_id === 1) {
                $profile->schema_id = $individualSchema->id;
                $profile->parent_id = 1;
                $profile->avatar = '/images/individual/avatar'.rand(1, 5).'.png';
                $profile->first_name = fake()->firstName();
                $profile->last_name = fake()->lastName();
                $profile->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $profile->email = fake()->email();
            }

            if ($dynamic_model_category_id === 2) {
                $profile->schema_id = $businessSchema->id;
                $profile->parent_id = 2;
                $profile->avatar = '/images/business/avatar'.rand(1, 5).'.png';
                $profile->company_name = fake()->company();
                $profile->cell_number = $phoneNumbers[array_rand($phoneNumbers)];
                $profile->email = fake()->email();
            }

            $profile->save();
        }
        /*-----------------------------Seed Profile - End------------------------------------------*/


        /*-----------------------------Seed Process - Start------------------------------------------*/

        /*-----------------------------Seed Process - Start------------------------------------------*/

    }
}
