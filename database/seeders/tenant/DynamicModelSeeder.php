<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use Illuminate\Database\Seeder;

class DynamicModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = Profile::orderBy('id')->get();

        foreach ($profiles as $profile) {
            $tableName = 'individual_1';
            if ($profile->profile_type_id == 2) {
                $tableName = 'business_2';
            }

            $individual = new DynamicModel();
            $individual->setTable($tableName);
            $individual->parent_id = $profile->id;
            $individual->save();
        }
    }
}
