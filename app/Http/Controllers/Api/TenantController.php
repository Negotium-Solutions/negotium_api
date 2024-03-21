<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TenantDatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class TenantController extends BaseAPIController
{
    /**
     * Get tenant(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        try{
            $query = isset($id) ? Tenant::find($id) : Tenant::query();

            $data = isset($id) ? $query : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No tenant record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'tenants successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created tenant
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required'],
            ['domain' => 'string|required'],
            ['first_name' => 'string|required'],
            ['last_name' => 'string|required'],
            ['email' => 'email|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $tenant = new Tenant();

            if ($tenant->save() === false) {
                throw new \RuntimeException('Could not save tenant');
            }

            $tenant->domains()->create(['domain' => $request->input('domain')]);

            $tenant->run(function () use ($request) {
                User::create([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'password' => $request->input('password')
                ]);

                // Run this using jobs
                if ($request->input('seed_default_data') == 1) {
                    $seeder = new TenantDatabaseSeeder();
                    $seeder->run($request->input('domain'));
                }
            });

            return $this->success(['id' => $tenant->id], 'tenant successfully created', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the tenant.
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $tenant = Tenant::find($id);
            if((!isset($tenant))){
                return $this->success([], 'No tenant record found to update', [], Response::HTTP_NOT_FOUND);
            }

            $old_value = Tenant::findOrFail($id);
            $new_value = $request->all();

            if ($tenant->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update tenant');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update a tenant', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'tenant successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the tenant.
     */
    public function delete($id) : Response
    {
        try {
            $tenant = Tenant::find($id);
            if((!isset($tenant))){
                return $this->success([], 'No tenant record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($tenant->delete() === false) {
                throw new \RuntimeException('Could not delete the tenant');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete a tenant', ['tenant_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
