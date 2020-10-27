<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{



    /**
     * @OA\Get(
     *     path="/product",
     *     tags={"Product Table List"},
     *     summary="Returns the products",
     *     description="Return the list of products from product table.",
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
     *          description="All Products",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Whouse Object, Whouse Object], 'message':'All Warehouse Products'}")
     *        )
     *     ),
     * )
     */

    public function index()
    {

        $product_list = Product::select(['id','pbarcode', 'pname', 'pclass', 'saleprice', 'buyprice','manufacturer','scientificn','active']);

        if (isset($_GET['pclass']))
            $product_list = $product_list->where('pclass', $_GET['pclass']);
        if (isset($_GET['searchbox']))
            $product_list = $product_list->where('pname', 'like' ,'%'.$_GET['searchbox'].'%')
                                ->orWhere('pbarcode', 'like' ,'%'.$_GET['searchbox'].'%');
        if (isset($_GET['pname']))
            $product_list = $product_list->where('pname', $_GET['pname']);
        if (isset($_GET['manufacturer']))
            $product_list = $product_list->where('manufacturer', $_GET['manufacturer']);
        if (isset($_GET['order_by']) && isset($_GET['sort_by'])) {
            $product_list = $product_list->orderBy($_GET['order_by'], $_GET['sort_by']);
        } else if (isset($_GET['order_by'])) {
            $product_list = $product_list->orderBy($_GET['order_by'], 'asc');
        }


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['products' => $product_list->get(), 'sum' => $product_list->sum('buyprice'), 'count' => $product_list->count()], 'message' => "All Products"]
        ], 200);
    }









   /**
     *@OA\Schema(
     *  schema="SaveProduct",
     *  title="Save Product model",
     *  description="Model for handeling product save through API",
     *  @OA\Property(
     *     property="pname",
     *     type="String",
     *     description="products name",
     *      example="Cocomo"
     *  ),
     * 
     *  @OA\Property(
     *     property="pclass",
     *     type="String",
     *     description="products class",
     *      example="abc"
     *  ),
     * 
     *   @OA\Property(
     *     property="pbarcode",
     *     type="String",
     *     description="products barcode",
     *      example="2342343242"
     *  ),
     * 
     *  @OA\Property(
     *     property="saleprice",
     *     type="string",
     *     description="products sale price",
     *      example="121"
     *  ),
     * 
     *  @OA\Property(
     *     property="buyprice",
     *     type="string",
     *     description="products buy price",
     *      example="100"
     *  ),
     * 
     *    @OA\Property(
     *     property="active",
     *     type="string",
     *     description="is product active",
     *      example="1"
     *  )
     * 
     *)
     */


    /**
     * @OA\Post(
     *     path="/product/save",
     *     tags={"Product save"},
     *     summary="Create product",
     *     description="User can create product through this api",
     *     operationId="save",
     *    @OA\RequestBody(
     *         description="save product object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SaveProduct")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="validation fails"
     *     ),
     *  @OA\Response(
     *         response="200",
     *         description="Product Saved Successfully."
     *     ),
     * )
     */




    public function save(Request $request){
        $validator = Validator::make($request->all(), [
            'pbarcode' => 'required',
            'pname' => 'required',
            'pclass' => 'required',
            'saleprice' => 'required',
            'buyprice' => 'required',
            'active' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'statusCode' => 400,
                'data' => ['data' => null, 'message' => $validator->messages()],
            ], 400);
        }

        $validatedData =  $request->all();
        $product = new Product();
        $product->timestamps = false;
        $product->pbarcode = $validatedData['pbarcode'];
        $product->pname = $validatedData['pname'];
        $product->pclass = $validatedData['pclass'];
        $product->saleprice = $validatedData['saleprice'];
        $product->buyprice = $validatedData['buyprice'];
        $product->active = $validatedData['active'];

        if ($product->save()) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => Product::find($product->id), 'message' => 'Product Saved Successfully.'],
            ], 200);
        }
    }


        /**
     * @OA\Get(
     *     path="/product/{id}",
     *     tags={"Product"},
     *     summary="Returns the product through barcode",
     *     description="Takes barcode in the query path and returns the relevant product.",
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
     *       @OA\Property(property="data", type="string", example="{'data' : {product Object}, 'message':'Product retrieved'}")
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

        $product = Product::where('pbarcode', $id)->first();

        if ($product) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $product, 'message' => "Product retrieved."]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'data' => ['data' => null, 'message' => "No product exists."]
            ], 404);
        }
    }



    /**
     * @OA\Get(
     *     path="/product/{id}/status/{status}",
     *     tags={"Product Status Change"},
     *     summary="Changes the status of product through barcode",
     *     description="Takes barcode in the query path and Changes the status of product.",
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
     *   @OA\Parameter(
     *     name="status",
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
     *       @OA\Property(property="data", type="string", example="{'data' : {product Object}, 'message':'Product Status Changed Successfully.'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */

    public function changeStatus($id,$status){
        $product = Product::where('id', $id)->first();
        $product->timestamps = false;
        $product->active = $status;
        if ($product->save()) {
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $product, 'message' => 'Product Status Changed Successfully.'],
            ], 200);
        }
    }
}
