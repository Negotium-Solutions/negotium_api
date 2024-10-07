<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Requests\Tenant\DynamicModelStepRequest;
use App\Http\Requests\Tenant\StepRequest;
use App\Models\Tenant\DynamicModelField;
use App\Models\Tenant\Schema;
use App\Models\Tenant\Step;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class DynamicModelStepController extends BaseApiController
{
    /**
     * Create a new step.
     *
     * @OA\Post(
     *        path="/{tenant}/dynami-model-step/create",
     *        summary="Create a new dynamic model step",
     *        operationId="createDynamicModelStep",
     *        tags={"DynamicModelStep"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=500,description="Internal server error")
     * )
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(DynamicModelStepRequest $request) : Response
    {
        try {
            $schema = Schema::find($request->input('id'));
            $schema->name = $request->input('name');
            $schema->save();

            foreach ( $request->input('steps') as $_step) {
                $step = Step::where('name', $_step['name'])->where('parent_id', $schema->id)->first();
                if (empty($step->id)) {
                    $step = new Step();
                    $step->name = $_step['name'];
                    $step->parent_id = $schema->id;

                    if ($step->save() === false) {
                        throw new \RuntimeException('Could not save step');
                    }
                    $step->order = $step->id;
                    $step->save();
                }
            }

            return $this->success(['id' => 1/*$step->id*/], 'Section successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create section.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
