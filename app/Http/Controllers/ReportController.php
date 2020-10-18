<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{


    /**
     * @OA\Get(
     *     path="/report/budget",
     *     tags={"Budget Report"},
     *     summary="Returns the budget report",
     *     description="Return the budget report.
     *      Report contains two parts (first half and its sum and count, and second half and its sum and count)",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *          description="Budget Report",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : ['first_half', 'first_half_sum_and_count', 'second_half', 'second_half_sum_and_count'], 'message':'Budget Report'}")
     *        )
     *     ),
     * )
     */

    public function budgetReport()
    {

        $first_half = DB::select("SELECT 'موجودات ثابتة' as detail, SUM(total) AS Ammount FROM  dbo.ie
            UNION
            SELECT  'مبلغ رصيد المخزن' as detail, ROUND(SUM(ROUND(buyprice * ROUND(cwhouse, 2), 2)), 2) AS Ammount FROM  dbo.whouse
            UNION
            SELECT  'رصيد الصندوق' as detail, ISNULL(SUM(vbox), 0) - ISNULL(SUM(pbox), 0) AS Ammount FROM     dbo.boxcase
            UNION
            SELECT mrtype as detail, SUM(mrdebt) AS Ammount FROM (SELECT  mrtype, SUM(mrdebt) AS mrdebt, mrname FROM  dbo.mrdebt GROUP BY mrtype, mrname HAVING (SUM(mrdebt) > 0)) AS derivedtbl_1 GROUP BY mrtype");

        $first_half_sum_and_count = DB::select("SELECT sum(Ammount) as total_amount, count(Ammount) as rows_count FROM(
            SELECT 'موجودات ثابتة' as detail, SUM(total) AS Ammount FROM  dbo.ie
            UNION
            SELECT  'مبلغ رصيد المخزن' as detail, ROUND(SUM(ROUND(buyprice * ROUND(cwhouse, 2), 2)), 2) AS Ammount FROM  dbo.whouse
            UNION
            SELECT  'رصيد الصندوق' as detail, ISNULL(SUM(vbox), 0) - ISNULL(SUM(pbox), 0) AS Ammount FROM     dbo.boxcase
            UNION
            SELECT mrtype as detail, SUM(mrdebt) AS Ammount FROM (SELECT  mrtype, SUM(mrdebt) AS mrdebt, mrname FROM  dbo.mrdebt GROUP BY mrtype, mrname HAVING (SUM(mrdebt) > 0)) AS derivedtbl_1 GROUP BY mrtype
            ) as tb1");


        $second_half = DB::select("SELECT mrtype as detail, SUM(mrdebt) AS Ammount 
            FROM 
                (SELECT mrtype, SUM(mrdebt) AS mrdebt, mrname FROM dbo.mrdebt GROUP BY mrtype, mrname HAVING (SUM(mrdebt) < 0)) 
            AS derivedtbl_1 
            GROUP BY mrtype");

        $second_half_sum_and_count = DB::select("SELECT sum(Ammount) as total_amount, count(Ammount) as rows_count FROM(
            SELECT mrtype as detail, SUM(mrdebt) AS Ammount 
            FROM 
                (SELECT mrtype, SUM(mrdebt) AS mrdebt, mrname FROM dbo.mrdebt GROUP BY mrtype, mrname HAVING (SUM(mrdebt) < 0)) 
            AS derivedtbl_1 
            GROUP BY mrtype
            ) as tb1");


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => [
                'data' =>
                ['first_half' => $first_half, 'first_half_sum_and_count' => $first_half_sum_and_count, 'second_half' => $second_half, 'second_half_sum_and_count' => $second_half_sum_and_count], 'message' => "Bugdet Report"
            ]
        ], 200);
    }



    /**
     * @OA\Get(
     *     path="/report/chart",
     *     tags={"Chart Report"},
     *     summary="Returns the Chart Report",
     *     description="Return the Chart Report.
     *        display type: amount or quantity, movement type: less or large, report type: customers or products",
     *     operationId="chartReport",
     *     @OA\Parameter(
     *     name="customer_name",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="movement_type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="display_type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Parameter(
     *     name="pname",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="pclass",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="governer",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     * @OA\Parameter(
     *     name="city",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     * @OA\Parameter(
     *     name="ie",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="manufacturer",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="rows",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *   @OA\Parameter(
     *     name="from_date",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="to_date",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *   @OA\Parameter(
     *     name="report_type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Chart Data",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : ['chart' : 'data'], 'message' : 'Chart Report data'}")
     *        )
     *     ),
     *        @OA\Response(
     *         response=400,
     *          description="Make sure from date, to date and rows are not empty",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="false"),
     *       @OA\Property(property="statusCode", type="integer", example="400"),
     *       @OA\Property(property="data", type="string", example="{'data' : ['data' : 'null'], 'message' : 'Make sure from date, to date and rows are not empty'}")
     *        )
     *     ),

     * )
     */

    public function chartReport()
    {

        $movement_type = isset($_GET['movement_type']) ? $_GET['movement_type'] : 'less';
        $display_type = isset($_GET['display_type']) ? $_GET['display_type'] : 'quantity';
        $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
        $to_date = isset($_GET['to_date']) ? $_GET['from_date'] : null;
        $rows = isset($_GET['rows']) ? $_GET['rows'] : null;
        $ie = isset($_GET['ie']) ? $_GET['ie'] : "sale";
        $report_type = isset($_GET['report_type']) ? $_GET['report_type'] : "customers";

        if ($from_date == null || $to_date == null || $rows == null) {
            return response()->json([
                'status' => false,
                'statusCode' => 400,
                'data' => ['data' => null, 'message' => "Make sure from date, to date and rows are not empty"]
            ], 400);
        }

        if ($report_type == "customers") {
            // if report type is customer
            $query = "SELECT TOP ($rows) dbo.adata.mrname, dbo.merchant.governer, dbo.merchant.city, SUM(dbo.adata.carton) AS Quantity, ROUND(SUM         (dbo.adata.total), 2) AS Total FROM dbo.adata LEFT OUTER JOIN dbo.merchant ON dbo.adata.mrname = dbo.merchant.mrname ";
            $groupBy = "GROUP BY dbo.adata.mrname, dbo.adata.ie, dbo.merchant.governer, dbo.merchant.city ";
            $filterQuery = "HAVING (SUM(dbo.adata.carton) <> 0) ";
        } else {
            $query = "SELECT TOP ($rows) pname, manufacturer, pclass, SUM(carton) AS Quantity, ROUND(SUM(dbo.adata.total), 2) AS Total FROM dbo.adata";
            $groupBy = " GROUP BY pname, manufacturer, pclass ";
            $filterQuery = "HAVING (pname IS NOT NULL AND pname <> N'') AND (SUM(carton) <> 0) ";
        }

        $whereQuery = " WHERE (ie = N'" . $ie . "') AND (dbo.adata.date1 >= '" . $to_date . "' and dbo.adata.date1 <= '" . $from_date . "') ";
        $orderBy = " ORDER BY Quantity ";

        if (isset($_GET['pclass']))
            $whereQuery .= " and pclass = '" . $_GET['pclass'] . "' ";
        if (isset($_GET['pname']))
            $whereQuery .=  " and pname = '" . $_GET['pname'] . "' ";
        if (isset($_GET['manufacturer']))
            $whereQuery .=  " and manufacturer = '" . $_GET['manufacturer'] . "' ";
        if (isset($_GET['customer_name']))
            $whereQuery .=  " and adata.mrname  = '" . $_GET['customer_name'] . "' ";
        if ($report_type == "customers") {
            if (isset($_GET['governer']))
                $whereQuery .=  " and governer  = '" . $_GET['governer'] . "' ";
            if (isset($_GET['city']))
                $whereQuery .=  " and city  = '" . $_GET['city'] . "' ";
        }

        if ($display_type == 'amount')
            $orderBy = " ORDER BY ROUND(SUM(dbo.adata.total), 2) ";

        $movement_type = $movement_type == 'less' ? 'asc' : 'desc';
        $orderBy .=  " $movement_type";

        $query = $query . $whereQuery . $groupBy . $filterQuery . $orderBy;

        $data = DB::select($query);

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['chart' => $data], 'message' => "Chart Report data"]
        ], 200);
    }
}
