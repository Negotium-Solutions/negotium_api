<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\CommunicationStatus;
use Illuminate\Database\Seeder;

class CommunicationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommunicationStatus::insert([
            ['name' => 'Read'],
            ['name' => 'Unread'],
            ['name' => 'Pending'],
            ['name' => 'Draft'],
            ['name' => 'Sent']
        ]);
    }
}
