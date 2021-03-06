<?php

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations\Get;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('user/login-inline',function(Request $request)    {
    $validator = Validator::make($request->all(), [
        'username' => 'required',
        'password' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'statusCode' => 200,
            'data' => $validator->messages(),
        ], 200);
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
        'statusCode' => 200,
        'data' => "User does not exists",
    ], 200);
});

Route::POST('user/login', [UserController::class, 'login']);