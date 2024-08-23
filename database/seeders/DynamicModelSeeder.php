<?php

namespace Database\Seeders;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            $individual  = new DynamicModel();
            $individual->setDynamicTable('individual_1');
            $individual->parent_id = $profile->id;
            $individual->save();

            $business  = new DynamicModel();
            $business->setDynamicTable('business_1');
            $business->parent_id = $profile->id;
            $business->save();
        }
    }
}
