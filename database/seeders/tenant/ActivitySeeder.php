<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Process Activities type_id = 1
        Activity::insert([
            ['name' => 'contact_name','label' => 'Contact Name', 'attributes' => 1, 'type_id' => 2, 'step_id' => 5],
            ['name' => 'contact_surname','label' => 'Contact Surname', 'attributes' => 1, 'type_id' => 2, 'step_id' => 5],
            ['name' => 'contact_phone','label' => 'Contact Phone', 'attributes' => 1, 'type_id' => 2, 'step_id' => 5],
            ['name' => 'contact_email','label' => 'Contact Email', 'attributes' => 1, 'type_id' => 2, 'step_id' => 5],
            ['name' => 'activity_01','label' => 'Activity 01', 'attributes' => 1, 'type_id' => 1, 'step_id' => 6],
            ['name' => 'activity_02','label' => 'Activity 02', 'attributes' => 1, 'type_id' => 2, 'step_id' => 6],
            ['name' => 'activity_03','label' => 'Activity 03', 'attributes' => 1, 'type_id' => 3, 'step_id' => 6],
            ['name' => 'activity_04','label' => 'Activity 04', 'attributes' => 1, 'type_id' => 4, 'step_id' => 7],
            ['name' => 'activity_05','label' => 'Activity 05', 'attributes' => 1, 'type_id' => 5, 'step_id' => 7],
            ['name' => 'activity_06','label' => 'Activity 06', 'attributes' => 1, 'type_id' => 6, 'step_id' => 8],
            ['name' => 'activity_07','label' => 'Activity 07', 'attributes' => 1, 'type_id' => 7, 'step_id' => 8],
            ['name' => 'activity_08','label' => 'Activity 08', 'attributes' => 1, 'type_id' => 8, 'step_id' => 8],
        ]);
    }
}
