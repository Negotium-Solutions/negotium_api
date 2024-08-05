<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

class DropTenantDatabases extends Command
{
    protected $signature = 'tenants:drop-databases';
    protected $description = 'Drop databases for all tenants';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle() : void
    {
        // Loop through all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Drop the tenant's database
            $databaseName = 'tenant' . $tenant->id;
            $this->info("Dropping database: $databaseName");
            DB::statement("DROP DATABASE IF EXISTS `$databaseName`");
        }
    }
}
