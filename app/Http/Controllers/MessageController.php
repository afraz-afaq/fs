<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => $validator->messages(),
            ], 200);
        }
        $requestData = $request->all();
        $user = User::where('uname', $requestData['username'])
            ->andWhere('password', $requestData['username'])
            ->first();
        if ($user)
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => $user,
            ], 200);
    }


}
