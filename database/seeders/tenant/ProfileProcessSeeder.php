<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Process;
use App\Models\Tenant\Profile;
use App\Models\Tenant\ProfileProcess;
use App\Models\Tenant\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Session;

class ProfileProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileTypes = Schema::where('dynamic_model_type_id', 1)->get();
        $processes = Schema::where('dynamic_model_type_id', 2)->limit(4)->get();
        foreach ($profileTypes as $profileType) {
            Session::put('table_name', $profileType->table_name);
            $profiles = DynamicModel::where('schema_id', $profileType->id)->limit(4)->get();
            foreach ($profiles as $key => $profile) {
                foreach ($processes as $process) {
                    $profileProcess = new ProfileProcess();
                    $profileProcess->profile_id = $profile->id;
                    $profileProcess->process_id = $process->id;
                    $profileProcess->save();
                }
            }
        }
    }
}
