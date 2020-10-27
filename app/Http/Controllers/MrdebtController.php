<?php

namespace App\Http\Controllers;

use App\Models\Mrdebt;
use Illuminate\Http\Request;

class MrdebtController extends Controller
{

    /**
     * @OA\Get(
     *     path="/mrdebt",
     *     tags={"Mrdebt Table Customers Account List"},
     *     summary="Returns the mrdebt customer accounts list",
     *     description="Return the list of customers from mrdebt table.",
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
     *          description="All Mrdebt Customers Accounts",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Mrdebt Object, Mrdebt Object], 'message':'All Mrdebt Customers Accounts'}")
     *        )
     *     ),
     * )
     */


    public function index()
    {

        $debts_list = Mrdebt::select(['mrname', 'mrdebt', 'mrtype', 'governer', 'city']);

        $mrname = "";
        if (isset($_GET['usertype'])) {
            if (isset($_GET['usertype']) == "customer")
                $mrname = $_GET['company_name'];
            else
                $mrname = $_GET['customer_name'];
        }


        if ($mrname && $mrname != "")
            $debts_list = $debts_list->where('mrname', $mrname);
        if (isset($_GET['type']))
            $debts_list = $debts_list->where('mrtype', $_GET['type']);
        if (isset($_GET['address']))
            $debts_list = $debts_list->where('governer', $_GET['address']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $debts_list = $debts_list->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $debts_list = $debts_list->orderBy($_GET['order_by'], 'asc');
        }

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['debts' => $debts_list->get(), 'sum' => $debts_list->sum('mrdebt'), 'count' => $debts_list->count()], 'message' => "All mrdepts"]
        ], 200);
    }
}
