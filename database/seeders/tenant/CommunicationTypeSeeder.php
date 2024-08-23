<?php

namespace Database\Seeders\tenant;

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
            ['name' => 'Email', 'color' => fake()->colorName],
            ['name' => 'WhatsApp', 'color' => fake()->colorName],
            ['name' => 'SMS', 'color' => fake()->colorName],
            ['name' => 'In-System', 'color' => fake()->colorName]
        ]);
    }
}
