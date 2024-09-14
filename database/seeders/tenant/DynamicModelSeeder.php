<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;

class DynamicModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = Profile::get();
        $individual = Schema::where('name', 'like', '%individual%')->first();
        $business = Schema::where('name', 'like', '%business%')->first();

        foreach ($profiles as $profile) {
            $tableName = $individual->name;
            if ($profile->profile_type_id == 2) {
                $tableName = $business->name;
            }

            $individual = new DynamicModel();
            $individual->setTable($tableName);
            $individual->parent_id = $profile->id;
            $individual->save();
        }
    }
}
