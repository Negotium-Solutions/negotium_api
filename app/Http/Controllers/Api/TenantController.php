<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class TenantController extends BaseAPIController
{
    /**
     * Get user(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? Tenant::find($id) : Tenant::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'tenants successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created process
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
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
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
            });

            return $this->success(['id' => $tenant->id], 'Tenant successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create tenant.', []);
        }
    }

    /**
     * Update the user.
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $tenant = Tenant::findOrFail($id);
            $old_value = Tenant::findOrFail($id);
            $new_value = $request->all();

            if ($tenant->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update tenant');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update a tenant', $request->all(), Response::HTTP_BAD_REQUEST);
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

            if ($tenant->delete() === false) {
                throw new \RuntimeException('Could not delete the tenant');
            }

            return $this->success([], 'tenant successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete a tenant', ['tenant_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
