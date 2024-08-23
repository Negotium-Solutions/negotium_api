<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Communication;
use Illuminate\Database\Seeder;

class CommunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Communication::factory()->count(100)->create();
    }
}
