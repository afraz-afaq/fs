<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 OpenApi",
 *      description="FS Project",
 *      @OA\Contact(
 *          email="afrazafaq96@gmail.com"
 *      ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

/**
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="L5 Swagger OpenApi dynamic host server"
 *  )
 *
 *  @OA\Server(
*      url="https://projects.dev/api/v1",
 *      description="L5 Swagger OpenApi Server"
 * )
 */

class UserController extends Controller
{
   

/**
     *@OA\Schema(
     *  schema="UserLogin",
     *  title="User login model",
     *  description="Model for handeling user login through API",
     *  @OA\Property(
     *     property="username",
     *     type="String",
     *     description="Account username of the user",
     *      example="Farhad SINDI"
     *  ),
     * 
     *  @OA\Property(
     *     property="password",
     *     type="String",
     *     description="Account password of the user",
     *      example="123"
     *  ),
     *)
     */

    
    /**
     * @OA\Post(
     *     path="/user/login",
     *     tags={"User Login"},
     *     summary="Create user",
     *     description="User can login through this api",
     *     operationId="login",
     *     @OA\Response(
     *         response="200",
     *         description="logined user object"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Username or password missing"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     *     @OA\RequestBody(
     *         description="Login user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserLogin")
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'statusCode' => 400,
                'data' => $validator->messages(),
            ], 400);
        }
        $requestData = $request->all();
        $user = User::where('uname', $requestData['username'])
            ->where('upass', $requestData['password'])
            ->where('active',1)
            ->where('ios',1)
            ->where('cislocked',0)
            ->where('cisdelete',0)
            ->first();
        if ($user)
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => $user,
            ], 200);
        else
        return response()->json([
            'status' => false,
            'statusCode' => 404,
            'data' => "User does not exists",
        ], 404);
    }
}
