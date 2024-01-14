<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class TenantController extends BaseAPIController
{
    /**
     * Store a newly created process
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['domain' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            // $tenant = new Tenant();
            // $tenant->domain = $request->domain;
            $tenant = Tenant::create(['id' => $request->name]);
            $tenant->domains()->create(['domain' => $request->domain]);

            if ($tenant->save() === false) {
                throw new \RuntimeException('Could not save tenant');
            }

            return $this->success(['id' => $tenant->id], 'Tenant successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create tenant.', []);
        }
    }
}
