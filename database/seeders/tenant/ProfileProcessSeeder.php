<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Process;
use App\Models\Tenant\Profile;
use App\Models\Tenant\ProfileProcess;
use Illuminate\Database\Seeder;

class ProfileProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profile_id1 = Profile::orderBy('created_at')->first()->id;
        $profile_id2 = Profile::orderBy('created_at')->first()->id;
        $processes = Process::limit(10)->get();
        ProfileProcess::insert([
            ['profile_id' => $profile_id1, 'process_id' => $processes[0]->id],
            ['profile_id' => $profile_id1, 'process_id' => $processes[1]->id],
            ['profile_id' => $profile_id1, 'process_id' => $processes[2]->id],
            ['profile_id' => $profile_id1, 'process_id' => $processes[3]->id],
            ['profile_id' => $profile_id1, 'process_id' => $processes[4]->id],
            ['profile_id' => $profile_id2, 'process_id' => $processes[5]->id],
            ['profile_id' => $profile_id2, 'process_id' => $processes[6]->id],
            ['profile_id' => $profile_id2, 'process_id' => $processes[7]->id],
            ['profile_id' => $profile_id2, 'process_id' => $processes[8]->id],
            ['profile_id' => $profile_id2, 'process_id' => $processes[9]->id]
        ]);
    }
}
