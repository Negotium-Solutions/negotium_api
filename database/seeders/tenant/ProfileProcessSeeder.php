<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ProfileProcess;
use Illuminate\Database\Seeder;

class ProfileProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfileProcess::insert([
           ['profile_id' => 1, 'process_id' => 1],
           ['profile_id' => 1, 'process_id' => 2],
           ['profile_id' => 1, 'process_id' => 3],
           ['profile_id' => 1, 'process_id' => 4],
           ['profile_id' => 1, 'process_id' => 5],
           ['profile_id' => 2, 'process_id' => 6],
           ['profile_id' => 2, 'process_id' => 7],
           ['profile_id' => 2, 'process_id' => 8],
           ['profile_id' => 2, 'process_id' => 9],
           ['profile_id' => 2, 'process_id' => 10],
        ]);
    }
}
