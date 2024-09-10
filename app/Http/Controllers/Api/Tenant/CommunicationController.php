<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Helpers\Helper;
use App\Mail\Tenant\CommunicationEmail;
use App\Models\Tenant\Communication;
use App\Models\Tenant\Profile;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest as PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Rikscss\BaseApi\Http\Controllers\BaseApiController;
use Stancl\Tenancy\Contracts\Tenant;

class CommunicationController extends BaseApiController
{
    /**
     * Get communication(s)
     *
     * @OA\Get(
     *       path="/{tenant}/communication/{id}",
     *       summary="Get a Communication",
     *       operationId="getCommunication",
     *       tags={"Communication"},
     *       security = {{"BearerAuth": {}}},
     *       description="Authenticate using a bearer token",
     *       @OA\Parameter(name="id", description="Communication Id", required=false, in="path", @OA\Schema( type="string" )),
     *       @OA\Response(response=200,description="Successful operation",@OA\JsonContent()),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=500,description="Internal server error")
     *  ),
     *
     * @OA\Get(
     *       path="/{tenant}/Communication",
     *       summary="Get communications",
     *       operationId="getCommunications",
     *       tags={"Communication"},
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
            $query = isset($id) ? Communication::where('id', $id) : Communication::query();

            if ($request->has('with') && ($request->input('with') != '')) {
                $_with = explode(',', $request->input('with'));
                $query = $query->with($_with);
            }

            $data = isset($id) ? $query->first() : $query->get();

            if((isset($id) && !isset($data)) || (!isset($id) && count($data) == 0)){
                return $this->success([], 'No communication record(s) found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->success($data, 'communications successfully retrieved', [], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to retrieve tenant.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new communication.
     *
     * @OA\Post(
     *        path="/{tenant}/communication/create",
     *        summary="Create a new communication",
     *        operationId="createCommunication",
     *        tags={"Communication"},
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
            'subject' => 'string|required',
            'communication' => 'string|required',
            'user_email' => 'email|required',
            'profile_id' => 'integer|required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->user_email)->first();
        try {
            $reminder_datetime = date('Y-m-d H:i', strtotime($request->reminder_date.' '.$request->reminder_time));
            $communication = new Communication();
            $communication->subject = $request->subject;
            $communication->communication = $request->communication;
            $communication->user_id = $user->id;
            $communication->profile_id = $request->profile_id;
            $communication->reminder_datetime = $reminder_datetime;

            if ($communication->save() === false) {
                throw new \RuntimeException('Could not save communication');
            }

            return $this->success(['id' => $communication->id], 'communication successfully created.', $request->all(), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage(), 'An error occurred while trying to create communication.', [],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a communication BY ID.
     *
     * @OA\Put(
     *        path="/{tenant}/communication/update/{id}",
     *        summary="Update a Communication",
     *        operationId="updateCommunication",
     *        tags={"Communication"},
     *        security = {{"BearerAuth": {}}},
     *        description="Authenticate using a bearer token",
     *        @OA\Parameter(name="id", description="Communication Id", required=true, in="path", @OA\Schema( type="string" )),
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
            $communication = Communication::find($id);
            if((!isset($communication))){
                return $this->success([], 'No communication record found to update', [], Response::HTTP_NO_CONTENT);
            }
            $old_value = Communication::findOrFail($id);
            $new_value = $request->all();

            if ($communication->updateOrFail($request->all()) === false) {
                throw new \RuntimeException('Could not update communication');
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to update the communication', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'communication successfully updated', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    public function sendEmail(Request $request, $id) : Response {
        $validator = \Validator::make($request->all(), [
            'to' => 'required|array',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profile = Profile::find($id);

            Mail::to($request->to)
                ->cc($request->cc)
                ->bcc($request->bcc)
                ->send(new CommunicationEmail($profile->profile_name, $request->subject, $request->message));

            $communication = new Communication();
            $communication->profile_id = $id;
            $communication->to = implode(';', $request->to);
            $communication->cc = !empty($request->cc) ? implode(';', $request->cc) : null;
            $communication->bcc = !empty($request->bcc) ? implode(';', $request->bcc) : null;
            $communication->subject = $request->subject;
            $communication->message = $request->message;
            $communication->communication_type_id = $request->communication_type_id;
            $communication->status_id = Communication::STATUS_SENT;
            $communication->user_id = Auth::user()->id;

            $old_value = [];
            $new_value = $request->all();

            if ($communication->save() === false) {
                return $this->error([], 'Failed to save communication email', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to send a communication email', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'communication email successfully sent', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    public function sendSMS(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(), [
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profile = Profile::find($id);

            $cell_number = Helper::replaceNumberPrefix($profile->cell_number, '27');
            $payload = [
                "messages" => [
                    "destinations" => ["to" => $cell_number],
                    "from" => app(Tenant::class)->configs->infobip_phone_number,
                    "text" => $request->input('message')
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'App '. app(Tenant::class)->configs->infobip_api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post(app(Tenant::class)->configs->infobip_base_url.'/sms/2/text/advanced',
                $payload
            );

            $responseData = json_decode($response->getBody(), true);

            if ( $response->getStatusCode() === 200 ) {
                // Todo: Send SMS
                $communication = new Communication();
                $communication->profile_id = $id;
                $communication->message = $request->message;
                $communication->communication_type_id = $request->communication_type_id;
                $communication->status_id = Communication::STATUS_SENT;
                $communication->user_id = Auth::user()->id;

                $old_value = [];
                $new_value = $request->all();

                if ($communication->save() === false) {
                    return $this->error([], 'Failed to save an sms', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $message = 'InfoBip API: '.(isset($responseData['requestError']['serviceException']['text']) ? $responseData['requestError']['serviceException']['text'] : 'Error sending sms.');
                return $this->error(['payload' => $payload, 'profile' => $profile], $message, $request->all(), $response->getStatusCode());
            }

        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to send an sms', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'sms successfully sent', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }


    public function sendWhatsApp(Request $request, $id) : Response
    {
        $validator = \Validator::make($request->all(), [
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Input validation error', $request->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $profile = Profile::find($id);

            $cell_number = Helper::replaceNumberPrefix($profile->cell_number, '27');
            $payload = [
                "messages" => [
                    "from" => app(Tenant::class)->configs->infobip_phone_number,
                    "to" => $cell_number,
                    "messageId" => "19805861-501c-4df0-aa04-66f2c3b48837",
                    "content" => [
                        "templateName" => "message_test",
                        "templateData" => [
                            "body" => [
                                "placeholders" => $request->input('message')
                            ]
                        ],
                        "language" => "en"
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'App '. app(Tenant::class)->configs->infobip_api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post(app(Tenant::class)->configs->infobip_base_url.'/whatsapp/1/message/template',
                $payload
            );

            $responseData = json_decode($response->getBody(), true);

            if ( $response->getStatusCode() === 200 ) {
                // Todo: Send SMS
                $communication = new Communication();
                $communication->profile_id = $id;
                $communication->message = $request->message;
                $communication->communication_type_id = $request->communication_type_id;
                $communication->status_id = Communication::STATUS_SENT;
                $communication->user_id = Auth::user()->id;

                $old_value = [];
                $new_value = $request->all();

                if ($communication->save() === false) {
                    return $this->error([], 'Failed to save whatsApp', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $message = 'InfoBip API: '.(isset($responseData['requestError']['serviceException']['text']) ? $responseData['requestError']['serviceException']['text'] : 'Error sending whatsApp.');
                return $this->error(['payload' => $payload, 'profile' => $profile], $message, $request->all(), $response->getStatusCode());
            }

        } catch (Throwable $exception) {
            return $this->error($exception->getMessage(), 'There was an error trying to send whatsApp', $request->all(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success([], 'whatsApp successfully sent', $request->all(), Response::HTTP_OK, $old_value, $new_value);
    }

    /**
     * Delete a Communication by ID.
     *
     * @OA\Delete(
     *      path="/{tenant}/communication/delete/{id}",
     *      operationId="deleteCommunicationById",
     *      tags={"Communication"},
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
            $communication = Communication::find($id);
            if((!isset($communication))){
                return $this->success([], 'No communication record found to delete', [], Response::HTTP_NOT_FOUND);
            }

            if ($communication->delete() === false) {
                throw new \RuntimeException('Could not delete the communication');
            }

            return response()->noContent();
        } catch (\Throwable $exception) {
            return $this->error([$exception->getMessage()], 'There was an error trying to delete the communication', ['communication_id' => $id], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
