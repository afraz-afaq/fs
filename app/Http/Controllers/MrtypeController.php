<?php

namespace App\Http\Controllers;

use App\Models\Mrtype;
use Illuminate\Http\Request;

class MrtypeController extends Controller
{


/**
     * @OA\Get(
     *     path="/mrtype",
     *     tags={"Mrtype Table List"},
     *     summary="Returns the types of mrdebt list",
     *     description="Return the list of mrdebt types from mrtype table.",
     *     operationId="index",

     *     @OA\Response(
     *         response=200,
     *          description="All Mrtypes",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Mrtype Object, Mrtype Object], 'message':'All Mrtypes'}")
     *        )
     *     ),
     * )
     */


    public function index(){
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['mrtypes' => Mrtype::get()], 'message' => "All mrtypes"]
        ], 200);
    }
}
