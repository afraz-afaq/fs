<?php

namespace App\Http\Controllers;

use App\Models\Mrdebt;
use Illuminate\Http\Request;

class MerchantController extends Controller
{

    /**
     * @OA\Get(
     *     path="/merchant",
     *     tags={"Get Merchant"},
     *     summary="Returns Merchant",
     *     description="Returns Merchant",
     *     operationId="index",
     *  @OA\Parameter(
     *     name="mrname",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Parameter(
     *     name="type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="address",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="order_by",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Mercant",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Mercant Object, Mercant Object], 'message':'Mercant'}")
     *        )
     *     ),
     * )
     */


    public function index()
    {

        $data = Mrdebt::select(['mrname', 'mrtype', 'governer', 'city', 'mobile1']);

        if (isset($_GET['mrname']))
            $data = $data->where('mrname', $_GET['mrname']);
        if (isset($_GET['type']))
            $data = $data->where('mrtype', $_GET['type']);
        if (isset($_GET['address']))
            $data = $data->where('city', $_GET['address']);
        if (isset($_GET['mobile']))
            $data = $data->where('mobile1', $_GET['mobile']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $data = $data->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $data = $data->orderBy($_GET['order_by'], 'asc');
        }

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['merchant' => $data->get(), 'count' => $data->count()], 'message' => "Merchant"]
        ], 200);
    }
}
