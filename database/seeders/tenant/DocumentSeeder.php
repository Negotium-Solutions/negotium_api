<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::factory(25)->create();
    }
}
