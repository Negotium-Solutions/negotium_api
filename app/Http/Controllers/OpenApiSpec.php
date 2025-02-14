<?php

namespace App\Http\Controllers;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User related operations"
 * )
 * @OA\Info(
 *     version="1.0.0",
 *     title="Negotium Solutions Swagger API Documentation",
 *     description="This is a Negotium swagger API documentation. You can find out more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net,#swagger](http://swagger.io/irc/).For this sample, you can use the api key `special-key` to test the authorization filters",
 *     @OA\Contact(name="Admin", email="admin@negotium-solutions.com")
 * )
 * @OA\Server(
 *     url="https://api-staging.negotium-solutions.com/api",
 *     description="This is the Negotium Solutions Swagger API Server"
 * )
 */
class OpenApiSpec extends Controller
{
    /**
     * Delete notification by ID.
     *
     * @OA\Delete(
     *      path="/api/v0.0.2/notifications/{id}",
     *      operationId="deleteNotificationById",
     *      tags={"Example"},
     *      @OA\Parameter(name="id", in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     *
     * @param Request $request
     * @param AppNotification $notification
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Request $request, AppNotification $notification) {
        //
    }
}
