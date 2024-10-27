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
        $processes = Schema::with(['groups'])->where('dynamic_model_type_id', 2)->limit(4)->get();
        foreach ($profileTypes as $profileType) {
            Session::put('table_name', $profileType->table_name);
            $profiles = DynamicModel::where('schema_id', $profileType->id)->limit(4)->get();
            foreach ($profiles as $profile) {
                foreach ($processes as $process) {
                    $profileProcess = new ProfileProcess();
                    $profileProcess->profile_id = $profile->id;
                    $profileProcess->process_id = $process->id;
                    $profileProcess->step_id = isset($process->groups[0]->id) ? $process->groups[0]->id : null;
                    $profileProcess->process_status_id = 1;
                    $profileProcess->save();

                    Session::put('table_name', $process->table_name);
                    $_process = new DynamicModel();
                    $_process->schema_id = $process->id;
                    $_process->parent_id = $profileProcess->id;
                    $_process->save();
                }
            }
        }
    }
}
