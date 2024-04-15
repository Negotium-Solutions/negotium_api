<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = new Tenant();
        $tenant->save();
        $tenant->domains()->create(['domain' => 'negotium-solutions.co.za']);
    }
}
