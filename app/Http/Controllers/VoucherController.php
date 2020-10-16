<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{

    /**
     * @OA\Get(
     *     path="/voucher",
     *     tags={"Voucher List"},
     *     summary="Returns the voucher list",
     *     description="Return the list of vouchers.
     *       Order by Fields: mrname => Customer, date1 => date, itemno => Item no, amount => arrivel",
     *     operationId="index",
     *     @OA\Parameter(
     *     name="mrname",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="date1",
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
     *          description="All Vouchers",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Voucher Object, Voucher Object], 'message':'All Vouchers'}")
     *        )
     *     ),
     * )
     */

    public function index()
    {

        $vouchers = Invoice::select(['mrname', 'itemno', 'date1', 'arrivel', 'comment'])
            ->where('ie', 'voucher');


        if (isset($_GET['mrname']))
            $vouchers = $vouchers->where('mrname', $_GET['mrname']);
        if (isset($_GET['date1']))
            $vouchers = $vouchers->where('date1', $_GET['date1']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $vouchers = $vouchers->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $vouchers = $vouchers->orderBy($_GET['order_by'], 'asc');
        }


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['vouchers' => $vouchers->get(), 'sum' => $vouchers->sum('arrivel'), 'count' => $vouchers->count()], 'message' => "All Vouchers"]
        ], 200);
    }
}