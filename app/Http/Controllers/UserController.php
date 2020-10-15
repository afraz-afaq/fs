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
                'data' => ['data' => null, 'message' => $validator->messages()],
            ], 400);
        }
        $requestData = $request->all();
        $user = User::where('uname', $requestData['username'])
            ->where('upass', $requestData['password'])
            ->where('active', 1)
            ->where('cislocked', 0)
            ->where('cisdelete', 0)
            ->first();
        if ($user)
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $user, 'message' => 'User logined successfully.'],
            ], 200);
        else
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'data' => ['data' => $user, 'message' => "User does not exists"],
            ], 404);
    }



    /**
     *@OA\Schema(
     *  schema="UserSignup",
     *  title="User signup model",
     *  description="Model for handeling user signup through API",
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
     * 
     *   @OA\Property(
     *     property="fullname",
     *     type="String",
     *     description="Account fullname of the user",
     *      example="abc xyz"
     *  ),
     * 
     *  @OA\Property(
     *     property="governer",
     *     type="string",
     *     description="Chosen governer",
     *      example="abc"
     *  ),
     * 
     *  @OA\Property(
     *     property="company_name",
     *     type="string",
     *     description="Chosen company",
     *      example="abc"
     *  ),
     * 
     *    @OA\Property(
     *     property="city",
     *     type="string",
     *     description="Chosen city",
     *      example="xyz"
     *  ),
     * 
     *   @OA\Property(
     *     property="address",
     *     type="String",
     *     description="Account address of the user",
     *      example="abc 12 xyz"
     *  ),
     * 
     *   @OA\Property(
     *     property="mobile",
     *     type="String",
     *     description="Account mobile of the user",
     *      example="0987654322"
     *  ),
     *)
     */


    /**
     * @OA\Post(
     *     path="/user/signup",
     *     tags={"User Signup"},
     *     summary="Create user",
     *     description="User can signup through this api",
     *     operationId="signup",
     *    @OA\RequestBody(
     *         description="Signup user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserSignup")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="validation fails"
     *     ),
     *  @OA\Response(
     *         response="200",
     *         description="signup user object"
     *     ),
     * )
     */

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:App\Models\User,uname',
            'password' => 'required|min:6',
            'fullname' => 'required',
            'governer' => 'required',
            'company_name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'statusCode' => 400,
                'data' => ['data' => null, 'message' => $validator->messages()],
            ], 400);
        }

        $validatedData =  $request->all();
        $user = new User();

        $user->uname = $validatedData['username'];
        $user->upass = $validatedData['password'];
        $user->fullname = $validatedData['fullname'];
        $user->governer = $validatedData['governer'];
        $user->companyname = $validatedData['company_name'];
        $user->city = $validatedData['city'];
        $user->address = $validatedData['address'];
        $user->mobile1 = $validatedData['mobile'];
        $user->timestamps = false;
        $user->cislocked = 0;
        $user->cisdelete = 0;
        $user->active = 1;
        if ($user->save()) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => User::find($user->id), 'message' => 'User Saved Successfully.'],
            ], 200);
        }
    }


    /**
     * @OA\Get(
     *     path="/user/save-token",
     *     tags={"User Save Token"},
     *     summary="Save user token",
     *     description="Saves user firebase token",
     *     operationId="savedeviceToken",
     *     @OA\Parameter(
     *     name="token",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="user_id",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
  
     *     @OA\Response(
     *         response=200,
     *          description="All Invoices",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [flag : 1], 'message':'Token Saved'}")
     *        )
     *     ),
     * )
     */

    public function savedeviceToken(){
        $token = $_GET['token'];
        $userid = $_GET['user_id'];

        $user = User::findOrFail($userid);
        $user->firebase_token = $token;
        $user->timestamps = false;
        $flag = $user->save();
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Token Saved."]
        ], 200);
    }
}
