<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory([
            'first_name' => 'Klaas',
            'last_name' => 'Riks'
        ])->create();

        User::factory(4)->create();
    }
}
