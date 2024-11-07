<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\ProfileProcess;
use App\Models\Tenant\Schema as TenantSchema;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Session;

class ProfileProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileTypes = TenantSchema::where('dynamic_model_type_id', 1)->get();
        $processes = TenantSchema::with(['groups'])->where('dynamic_model_type_id', 2)->limit(4)->get();
        $users = User::orderBy('first_name')->get();
        foreach ($profileTypes as $profileType) {
            Session::put('schema_id', $profileType->id);
            $profiles = DynamicModel::where('schema_id', $profileType->id)->limit(4)->get();
            foreach ($profiles as $profile) {
                foreach ($processes as $process) {
                    $profileProcess = new ProfileProcess();
                    $profileProcess->profile_id = $profile->id;
                    $profileProcess->process_id = $process->id;
                    $profileProcess->step_id = isset($process->groups[0]->id) ? $process->groups[0]->id : null;
                    $profileProcess->started_by_user_id = $users[rand(0, count($users) - 1)]->id;
                    $profileProcess->process_status_id = 1;
                    $profileProcess->save();

                    Session::put('schema_id', $process->id);
                    $_process = new DynamicModel();
                    $_process->schema_id = $process->id;
                    $_process->parent_id = $profileProcess->id;
                    $_process->save();
                }
            }
        }
    }
}
