<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class StepController extends BaseAPIController
{
    /**
     * Get step(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? Step::find($id) : Step::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'steps successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created step 
     */
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(),
            ['name' => 'string|required']
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), 422);
        }

        try {
            $step = new Step();
            $step->name = $request->name;

            if ($step->save() === false) {
                throw new \RuntimeException('Could not save step');
            }

            return $this->success(['id' => $step->id], 'step successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create step.', []);
        }
    }

    /**
     * Update the the step
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
            $step = Step::findOrFail($id);
            $old_value = Step::findOrFail($id);
            $new_value = $request->all();

            if ($step->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the step');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the step', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'step successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the step
     */
    public function delete($id) : Response
    {
        try {
            $step = Step::find($id);

            if ($step->delete() === false) {
                throw new \RuntimeException('Could not delete the step');
            }

            return $this->success([], 'step successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the step', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
