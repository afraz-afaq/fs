<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{


    /**
     * @OA\Get(
     *     path="/notification",
     *     tags={"Notification Table List"},
     *     summary="Returns the notifications",
     *     description="Return the list of notifications.",
     *     operationId="index",
     * 
     *     @OA\Response(
     *         response=200,
     *          description="All Notifications",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Notification Object, Notification Object], 'message':'All Notifications'}")
     *        )
     *     ),
     * )
     */


    public function index(){

        $notifications = Notification::whereRaw("message_type = N'all'")
                            ->orderBy('id', 'desc')
                            ->get();

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['notification' => $notifications], 'message' => "All Notifications"]
        ], 200);
    }
}
