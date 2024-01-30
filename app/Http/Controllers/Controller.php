<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
* This is needed as zigorte package checks the default controller to check if all the criteria is met for swagger,
* otherwise it's throws an error
*
* @OA\Info(
*      version="1.0.0",
*      title="Negotium Swagger API Documentation",
*      termsOfService="http://swagger.io/terms/",
*      description="This is a flow swagger API documentation.  You can find out more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net,#swagger](http://swagger.io/irc/).For this sample, you can use the api key `special-key` to test the authorization filters",
*      @OA\Contact(
*          email="sandile@gmail.com"
*      ),
*      @OA\License(
*          name="Apache 2.0",
*          url="http://www.apache.org/licenses/LICENSE-2.0.html"
*      )
* )
* @OA\Server(
*      url=L5_SWAGGER_CONST_HOST
*     
* )
*
*/
class Controller extends BaseController
{    use AuthorizesRequests, ValidatesRequests;
}
