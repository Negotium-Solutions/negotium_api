<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantConfig;
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
        $tenant->domains()->create(['domain' => 'negotium-solutions.com']);

        $tenantConfig = new TenantConfig();
        $tenantConfig->tenant_id = $tenant->id;
        $tenantConfig->infobip_api_key = '5b3d9c37af225a20232d40acb9383a62-e66c13c6-8a5b-47de-980d-7695ce18ce6a';
        $tenantConfig->infobip_base_url = 'peyv9m.api.infobip.com';
        $tenantConfig->infobip_phone_number = '447491163443';
        $tenantConfig->save();
    }
}
