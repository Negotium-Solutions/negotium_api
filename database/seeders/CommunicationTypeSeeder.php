<?php

namespace Database\Seeders;

use App\Models\Tenant\CommunicationType;
use Illuminate\Database\Seeder;

class CommunicationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommunicationType::insert([
            ['name' => 'Email'],
            ['name' => 'WhatsApp'],
            ['name' => 'SMS'],
            ['name' => 'In-System'],
        ]);
    }
}
