<?php

namespace App\Http\Controllers;

use App\Models\Whouse;
use Illuminate\Http\Request;

class WhouseController extends Controller
{
    

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

    public function view($id){

        $product = Whouse::where('pbarcode',$id)->first();

        if($product){
            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $product, 'message' => "Warehouse Product retrieved."]
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'data' => ['data' => null, 'message' => "No such warehouse product exists."]
            ], 404);
        }

    }

}
