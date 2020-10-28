<?php

namespace App\Http\Controllers;

use App\Models\Whouse;
use Illuminate\Http\Request;

class WhouseController extends Controller
{

    /**
     * @OA\Get(
     *     path="/whouse",
     *     tags={"Whouse Table Product List"},
     *     summary="Returns the whouse products.",
     *     description="Return the list of products from whouse table. Order By: 'pbarcode', 'pname', 'pclass', 'cwhouse', 'saleprice','manufacturer','scientificn'",
     *     operationId="index",
     *     @OA\Parameter(
     *     name="pclass",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="pname",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="manufacturer",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Parameter(
     *     name="scientificn",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="order_by",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="All Warehouse Products",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Product Object, Product Object], 'message':'All Products'}")
     *        )
     *     ),
     * )
     */

    public function index()
    {

        $whouse_list = Whouse::select(['pbarcode', 'pname', 'pclass', 'cwhouse', 'saleprice', 'manufacturer', 'scientificn']);

        if (isset($_GET['pclass']))
            $whouse_list = $whouse_list->where('pclass', $_GET['pclass']);
        if (isset($_GET['searchbox']))
            $whouse_list = $whouse_list->where('pname', 'like', '%' . $_GET['searchbox'] . '%')
                ->orWhere('pbarcode', $_GET['searchbox']);
        if (isset($_GET['pname']))
            $whouse_list = $whouse_list->where('pname', $_GET['pname']);
        if (isset($_GET['manufacturer']))
            $whouse_list = $whouse_list->where('manufacturer', $_GET['manufacturer']);
        if (isset($_GET['scientificn']))
            $whouse_list = $whouse_list->where('scientificn', $_GET['scientificn']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $whouse_list = $whouse_list->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $whouse_list = $whouse_list->orderBy($_GET['order_by'], 'asc');
        }


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['whouses' => $whouse_list->get(), 'sum' => $whouse_list->sum('cwhouse'), 'count' => $whouse_list->count()], 'message' => "All mrdepts"]
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/whouse/{id}",
     *     tags={"whouse"},
     *     summary="Returns the whouse product through barcode",
     *     description="Takes barcode in the query path and returns the relevant whouse product.",
     *     operationId="view",
     * 
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *     @OA\Response(
     *         response=200,
     *          description="Table content list",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : {whouse Object}, 'message':'Warehouse Product retrieved'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */

    public function view($id)
    {

        $product = Whouse::where('pbarcode', $id)->first();

        if ($product) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $product, 'message' => "Warehouse Product retrieved."]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'data' => ['data' => null, 'message' => "No such warehouse product exists."]
            ], 404);
        }
    }
}
