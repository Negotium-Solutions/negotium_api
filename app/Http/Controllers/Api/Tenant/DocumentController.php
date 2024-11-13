<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\ApiInterface;
use App\Models\Tenant\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Stancl\Tenancy\Contracts\Tenant;

class DocumentController extends BaseAPIController implements ApiInterface
{
    /**
     * Get document(s)
     *
     * @OA\Get(
     *       path="/{tenant}/document/{id}",
     *       summary="Get a document",
     *       operationId="getDocument",
     *       tags={"Document"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Document Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/document",
     *       summary="Get documents",
     *       operationId="getDocuments",
     *       tags={"Document"},
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
            $query = isset($id) ? Document::with(['user'])->where('id', $id) : Document::with(['user']);

            if( $request->has('profile_id') ) {
                $query->where('profile_id', $request->get('profile_id'));
            }

            $documents = isset($id) ? $query->first() : $query->get();

            if (isset($id)){
                $documents['url'] = tenant_assets(app(Tenant::class), $documents->path);
            } else {
                foreach ($documents as $key => $document) {
                    $documents[$key]['url'] = tenant_assets(app(Tenant::class), $document->path);
                }
            }

            if((isset($id) && !isset($documents)) || (!isset($id) && count($documents) == 0)){
                return $this->success([], 'No document record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($documents, 'documents successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new document.
     *
     * @OA\Post(
     *        path="/{tenant}/document/create",
     *        summary="Create a new document",
     *        operationId="createDocument",
     *        tags={"Document"},
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
            'name' => 'string|required',
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240', // 10MB Max per file
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            foreach($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $originalName = date('Y_m_d_H_i_s').'_'.rand(1000, 9999).'_'.str_replace(' ', '_', $originalName);
                $path = $file->storeAs('documents', $originalName);

                $document = new Document();
                $document->name = $request->input('name');
                $document->path = $path;
                $document->type = $file->getClientMimeType();
                $document->size = $file->getSize();
                $document->profile_id = $request->input('profile_id');
                $document->user_id = Auth::user()->id;

                if ($document->save() === false) {
                    throw new \RuntimeException('Could not save document');
                }
            }

            return $this->success(['id' => $document->id], 'document successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create document.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a document BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/document/update/{id}",
     *        summary="Update a Document",
     *        operationId="updateDocument",
     *        tags={"Document"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Document Id", required=true, in="path", @OA\Schema( type="string" )),
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
            'name' => 'string|required',
            'file' => ['required|file', File::types(['doc', 'docx', 'pdf', 'jpeg', 'jpg', 'png', 'txt'])]
        ]);

        $hasFileErrors = false;
        $errorFileMessages = [];
        if ( !$request->hasFile('file') ) {
            $hasFileErrors = true;
            $errorFileMessages[] = 'The file field is required.';
        }

        if ($validator->fails() || $hasFileErrors) {
            if($hasFileErrors) {
                foreach ($errorFileMessages as $errorFileMessage) {
                    $validator->errors()->add('file', $errorFileMessage);
                }
            }
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('documents', 'public');

            $document = Document::find($id);
            if((!isset($document))){
                return $this->success([], 'No document record found to update', [], Response::HTTP_NOT_FOUND);
            }
            $old_value = Document::findOrFail($id);
            $new_value = $request->all();

            $document->name = $request->name;
            $document->path = $path;
            $document->type =  $uploadedFile->getClientMimeType();
            $document->size = $uploadedFile->getSize();
            $document->user_id = Auth::user()->id;

            if ($document->save() === false) {
                throw new \RuntimeException('Could not update document');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the document', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'document successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Document by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/document/delete/{id}",
     *      operationId="deleteDocumentById",
     *      tags={"Document"},
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
            $document = Document::find($id);
            if((!isset($document))){
                return $this->success([], 'No document record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($document->delete() === false) {
                throw new \RuntimeException('Could not delete the document');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the document', ['document_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function download($id) : Response
    {
        try {
            // Retrieve the document metadata
            $document = Document::findOrFail($id);

            if (Storage::exists($document->path)) {
                $fileContents = Storage::get($document->path); // Get the file contents
                $base64EncodedFile = base64_encode($fileContents); // Encode to Base64

                // Get MIME type for convenience
                $mimeType = Storage::mimeType($document->path);
                $dataUri = "data:$mimeType;base64," . $base64EncodedFile;

                $file = [
                    'file' => $dataUri,
                    'name' => $document->name,
                    'url' => tenant_assets(app(Tenant::class), $document->path)
                ];
            } else {
                return $this->error([], 'Document not found', ['document_id' => $id], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the document', ['document_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success($file, 'Document retrieved successfully.', [], Response::HTTP_OK, $document);
    }
}
