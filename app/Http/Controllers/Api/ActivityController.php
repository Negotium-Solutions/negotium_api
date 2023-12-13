<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class ActivityController extends BaseAPIController
{
    /**
     * Get activity(s) resource(s).
     */
    public function get(Request $request, $id = null) : Response
    {
        $query = isset($id) ? Activity::find($id) : Activity::query();

        $data = isset($id) ? $query : $query->get();

        return $this->success($data, 'activities successfully retrieved', [], Response::HTTP_OK);
    }

    /**
     * Store a newly created activity 
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
            $activity = new Activity();
            $activity->name = $request->name;

            if ($activity->save() === false) {
                throw new \RuntimeException('Could not save activity');
            }

            return $this->success(['id' => $activity->id], 'activity successfully created.', $request->all(), 200);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create activity.', []);
        }
    }

    /**
     * Update the the activity
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
            $activity = Activity::findOrFail($id);
            $old_value = Activity::findOrFail($id);
            $new_value = $request->all();

            if ($activity->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update the activity');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the activity', $request->all(), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], 'activity successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete the activity
     */
    public function delete($id) : Response
    {
        try {
            $activity = Activity::find($id);

            if ($activity->delete() === false) {
                throw new \RuntimeException('Could not delete the activity');
            }

            return $this->success([], 'activity successfully deleted', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the the activity', ['id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
