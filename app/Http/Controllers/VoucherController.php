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
     *   @OA\Parameter(
     *     name="usertype",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="company_name",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="searchbox",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="itemno",
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

        $vouchers = Invoice::select(['id', 'mrname', 'itemno', 'date1', 'arrivel', 'comment', 'isDelete'])
            ->where('ie', 'voucher');


        if (isset($_GET['usertype'])) {
            if (isset($_GET['usertype']) == "customer")
                $vouchers = $vouchers->where('mrname',  $_GET['company_name']);
        }

        if (isset($_GET['mrname']))
            $vouchers = $vouchers->where('mrname', $_GET['mrname']);

        if (isset($_GET['searchbox']))
            $vouchers = $vouchers->where('mrname', 'like', "'%" . $_GET['searchbox'] . "%'");


        if (isset($_GET['date1']))
            $vouchers = $vouchers->where('date1', $_GET['date1']);
        if (isset($_GET['itemno']))
            $vouchers = $vouchers->where('itemno', $_GET['itemno']);
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



    /**
     * @OA\Get(
     *     path="/voucher/delete",
     *     tags={"Voucher or Payvoucher Delete"},
     *     summary="Deletes the voucher or pay voucher",
     *     description="Deletes the Voucher or Pay Voucher",
     *     operationId="deleteProduct",
     * 
     *  @OA\Parameter(
     *     name="id",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Voucher or Payvoucher Delete.",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : {flag}, 'message':'Voucher/Pay Voucher Deleted.'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */
    public function deleteVoucher()
    {

        $isDelete = $_GET['id'];
        $flag = DB::update("UPDATE dbo.ie set isdelete = 1 where id = $isDelete");

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Voucher/Pay Voucher Deleted."]
        ], 200);
    }



    /**
     * @OA\Get(
     *     path="/voucher/pay",
     *     tags={"Pay Voucher List"},
     *     summary="Returns the pay voucher list",
     *     description="Return the list of pay vouchers.
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
     *   @OA\Parameter(
     *     name="usertype",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="company_name",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="searchbox",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="itemno",
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
     *       @OA\Property(property="data", type="string", example="{'data' : [Pay Voucher Object, Pay Voucher Object], 'message':'All Vouchers'}")
     *        )
     *     ),
     * )
     */

    public function pay()
    {

        $vouchers = Invoice::select(['id', 'mrname', 'itemno', 'date1', 'total', 'comment', 'isDelete'])
            ->where('ie', 'payvoucher');

        if (isset($_GET['usertype'])) {
            if (isset($_GET['usertype']) == "customer")
                $vouchers = $vouchers->where('mrname',  $_GET['company_name']);
        }

        if (isset($_GET['mrname']))
            $vouchers = $vouchers->where('mrname', $_GET['mrname']);

        if (isset($_GET['searchbox']))
            $vouchers = $vouchers->where('mrname', 'like', "'%" . $_GET['searchbox'] . "%'");

        if (isset($_GET['date1']))
            $vouchers = $vouchers->where('date1', $_GET['date1']);
        if (isset($_GET['itemno']))
            $vouchers = $vouchers->where('itemno', $_GET['itemno']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $vouchers = $vouchers->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $vouchers = $vouchers->orderBy($_GET['order_by'], 'asc');
        }


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['payvoucher' => $vouchers->get(), 'sum' => $vouchers->sum('total'), 'count' => $vouchers->count()], 'message' => "All Pay Vouchers"]
        ], 200);
    }
}
