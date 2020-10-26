<?php

namespace App\Http\Controllers;

use App\Models\Boxname;
use App\Models\Governer;
use App\Models\City;
use App\Models\Country;
use App\Models\Manufacturer;
use App\Models\Merchant;
use App\Models\Mrdebt;
use App\Models\Mrtype;
use App\Models\Pclass;
use App\Models\Product;
use App\Models\Scientificn;

class FilterController extends Controller
{
    const COUNTRY = 1;
    const GOVERNER = 2;
    const CITY = 3;
    const MRDEBT = 4;
    const MRTYPE = 5;
    const MERCHANT = 6;
    const PRODUCT = 7;
    const PCLASS = 8;
    const MANUFACTURER = 9;
    const BOXNAME = 10;
    const SCIENTIFICN = 11;



    /**
     * @OA\Get(
     *     path="/list/{id}",
     *     tags={"Filter Tables"},
     *     summary="Returns the db table",
     *     description="COUNTRY = 1, GOVERNER = 2, CITY = 3, MRDEBT = 4, MRTYPE = 5, MERCHANT = 6, PRODUCT = 7, PCLASS = 8, MANUFACTURER = 9, BOXNAME = 10, SCIENTIFICN = 11",
     *     operationId="getList",
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
     *       @OA\Property(property="data", type="string", example="{'data' : [{'id': 1, 'country': 'AB'}, {'id': 2, 'country': 'CD'}], 'message':'List retrieved'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such table exists",
     *     ),
     * )
     */

    public function getList($id)
    {

        $table = "";
        $field = "";
        switch ($id) {
            case self::COUNTRY:
                $table = Country::class;
                $field = "country";
                break;
            case self::GOVERNER:
                $table = Governer::class;
                $field = "governer";
                break;
            case self::CITY:
                $table = City::class;
                $field = "city";
                break;
            case self::MRDEBT:
                $table = Mrdebt::class;
                $field = "mrname";
                break;
            case self::MERCHANT:
                $table = Merchant::class;
                $field = "mrname";
                break;
            case self::MRTYPE:
                $table = Mrtype::class;
                $field = "mrtype";
                break;
            case self::PRODUCT:
                $table = Product::class;
                $field = "pname";
                break;
            case self::PCLASS:
                $table = Pclass::class;
                $field = "pclass";
                break;
            case self::MANUFACTURER:
                $table = Manufacturer::class;
                $field = "manufacturer";
                break;
            case self::BOXNAME:
                $table = Boxname::class;
                $field = "boxname";
                break;
            case self::SCIENTIFICN:
                $table = Scientificn::class;
                $field = "scientificn";
                break;
                // default:
                //     $table = Governer::class;
                //     $field = "governer";
        }
        if ($table != "") {

            $list = $table::select($field." as data")->get();

            return response()->json([
                'status' => true,
                'statusCode' => 200,
                'data' => ['data' => $list, 'message' => 'List retrieved'],
            ], 200);
        } else
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'data' => ['data' => null, 'message' => "Not such table exists"],
            ], 404);
    }
}
