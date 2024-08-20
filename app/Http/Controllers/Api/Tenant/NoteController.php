<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Models\Tenant\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;

class NoteController extends BaseApiController
{
    /**
     * Get note(s)
     *
     * @OA\Get(
     *       path="/{tenant}/note/{id}",
     *       summary="Get a Note",
     *       operationId="getNote",
     *       tags={"Note"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Note Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/Note",
     *       summary="Get notes",
     *       operationId="getNotes",
     *       tags={"Note"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  )
     *
     * @param Request $request
     * @param Request $id
     * @return Response
     * @throws Exception
     */
    public function get(Request $request, $id = null) : Response
    {
        try{
            $query = isset($id) ? Note::where('id', $id) : Note::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No note record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'notes successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new note.
     *
     * @OA\Post(
     *        path="/{tenant}/note/create",
     *        summary="Create a new note",
     *        operationId="createNote",
     *        tags={"Note"},
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
    public function create(Request $request) : Response
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'string|required',
            'last_name' => 'string|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $note = new Note();
            $note->first_name = $request->first_name;
            $note->last_name = $request->last_name;

            if ($note->save() === false) {
                throw new \RuntimeException('Could not save note');
            }

            return $this->success(['id' => $note->id], 'note successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create note.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a note BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/note/update/{id}",
     *        summary="Update a Note",
     *        operationId="updateNote",
     *        tags={"Note"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Note Id", required=true, in="path", @OA\Schema( type="string" )),
     *        @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *        @OA\Response(response=422,description="Input validation error"),
     *        @OA\Response(response=404,description="Not found")
     *   ),
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'string',
            'last_name' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $note = Note::find($id);
            if((!isset($note))){
                return $this->success([], 'No note record found to update', [], Response::HTTP_NO_CONTENT);
            }
            $old_value = Note::findOrFail($id);
            $new_value = $request->all();

            if ($note->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update note');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the note', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'note successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Note by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/note/delete/{id}",
     *      operationId="deleteNoteById",
     *      tags={"Note"},
     *      security = {{"BearerAuth": {}}},
     *      description="Authenticate using a bearer token",
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="string")),
     *      @OA\Response(response=204, description="No content"),
     *      @OA\Response(response=404, description="Not found")
     * )
     *
     * @param String $id
     * @return Response
     * @throws Exception
     */
    public function delete($id) : Response
    {
        try {
            $note = Note::find($id);
            if((!isset($note))){
                return $this->success([], 'No note record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($note->delete() === false) {
                throw new \RuntimeException('Could not delete the note');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the note', ['note_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
