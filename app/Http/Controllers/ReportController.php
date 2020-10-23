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



    /**
     * @OA\Get(
     *     path="/report/yearly",
     *     tags={"Yearly Report"},
     *     summary="Returns the Yearly Report",
     *     description="Return the Yearly Report. display type: products or customers, report for: value or percentage, result type: quantity or amount, report type: sale or buy",
     *     operationId="getBuySellYearlyReport",
     *     @OA\Parameter(
     *     name="customer_name",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="scientificn",
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
     *     name="result_type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     * @OA\Parameter(
     *     name="report_for",
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
     *       @OA\Property(property="data", type="string", example="{'data' : ['yearly' : 'data'], 'message' : 'Yearly Report data'}")
     *        )
     *     ),


     * )
     */

    public function getBuySellYearlyReport()
    {

        $query = "";
        $table = "";
        $GroupBy = "";
        $OrderBy = "";

        // $movement_type = isset($_GET['movement_type']) ? $_GET['movement_type'] : 'less';
        $display_type = isset($_GET['display_type']) ? $_GET['display_type'] : 'customers';
        // $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
        // $to_date = isset($_GET['to_date']) ? $_GET['from_date'] : null;
        // $rows = isset($_GET['rows']) ? $_GET['rows'] : null;
        // $ie = isset($_GET['ie']) ? $_GET['ie'] : "sale";
        $report_type = isset($_GET['report_type']) ? $_GET['report_type'] : "sale";
        $result_type = isset($_GET['result_type']) ? $_GET['result_type'] : "amount";
        $report_for = isset($_GET['report_for']) ? $_GET['report_for'] : "value";

        if ($report_type == "sale") {
            $table = "monthlysaledetail.";
            if ($display_type ==  "customers") {
                if ($result_type ==  "amount") {
                    $query = $this->queryforsaleAmountbycustomerReport($report_for);
                    $GroupBy = " GROUP BY dbo.merchant.mrname";
                    $OrderBy = " ORDER BY merchant.mrname";
                } else {
                    $query = $this->queryForsaleQuantitybycustomertReport($report_for);
                    $GroupBy = " GROUP BY dbo.merchant.mrname, dbo.merchant.governer + N'.' + dbo.merchant.city, dbo.merchant.mrtype";
                    $OrderBy = " ORDER BY merchant.mrname";
                }
            } else {
                if ($result_type ==  "amount") {
                    $query = $this->queryforsaleAmountbyproductReport($report_for);
                    $GroupBy = " GROUP BY monthlysaledetail.pname, monthlysaledetail.pclass";
                    $OrderBy = " ORDER BY monthlysaledetail.pname";
                } else {
                    $query = $this->queryForsaleQuantitybyproductReport($result_type);
                    $GroupBy = " GROUP BY monthlysaledetail.pname";
                    $OrderBy = " ORDER BY monthlysaledetail.pname";
                }
            }
        } else {
            $table = "monthlybuy.";
            if ($result_type ==  "amount") {
                $query = $this->queryForYearlyBuyAmountReport($report_for);
                $GroupBy = " GROUP BY monthlybuy.pname";
                $OrderBy = " ORDER BY monthlybuy.pname";
            } else {
                $query = $this->queryForYearlyBuyQuantityReport($report_for);
                $GroupBy = " GROUP BY monthlybuy.pname,monthlybuy.pnamee, monthlybuy.pclass, dbo.whouse.manufacturer";
                $OrderBy = " ORDER BY monthlybuy.pname";
            }
        }

        $filterQuery = "";


        if (isset($_GET['pbarcode']))
            $filterQuery .= " and " . $table . "pbarcode = '" . $_GET['pbarcode'] . "' ";
        if (isset($_GET['pclass']))
            $filterQuery .= " and " . $table . "pclass = '" . $_GET['pclass'] . "' ";
        if (isset($_GET['pname']))
            $filterQuery .=  " and " . $table . "pname = '" . $_GET['pname'] . "' ";
        if (isset($_GET['manufacturer']))
            $filterQuery .=  " and " . $table . "manufacturer = '" . $_GET['manufacturer'] . "' ";
        if (isset($_GET['scientificn']))
            $filterQuery .=  " and " . $table . "scientificn = '" . $_GET['scientificn'] . "' ";
        if (isset($_GET['customer_name']))
            $filterQuery .=  " and " . $table . "mrname  = '" . $_GET['customer_name'] . "' ";

        $query = $query .  $filterQuery . $GroupBy . $OrderBy;

        $data = DB::select($query);


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['yearly' => $data], 'message' => "Yearly Report data"]
        ], 200);
    }



    /**
     * @OA\Get(
     *     path="/report/buy-sell",
     *     tags={"buy sell report"},
     *     summary="Returns Buy Sell Report",
     *     description="Return the Buy Sell Report. display type: short or detail, Report type: quantity or amount, report type: fwhouse, draftbuy, returnbuy, buy, draftsale, sale, returnsale, loss",
     *     operationId="getBuySellYearlyReport",
     *     @OA\Parameter(
     *     name="customerName",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="scientificn",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="displayType",
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
     *     name="manufacturer",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *   @OA\Parameter(
     *     name="reportType",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     * 
     *    @OA\Parameter(
     *     name="barcode",
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
     *       @OA\Property(property="data", type="string", example="{'data' : ['buy-sell' : 'data'], 'message' : 'Buy Sell Report.'}")
     *        )
     *     ),


     * )
     */


    public function buySellReport()
    {
        $displayType = isset($_GET['displayType']) ? $_GET['displayType'] : "";

        if ($displayType == 'short')
            $query = $this->getBuySellReportShort($_GET);
        else
            $query = $this->getBuySellReportDetailed($_GET);

        $data = DB::select($query);


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['buy-sell' => $data], 'message' => "Buy Sell Report."]
        ], 200);
    }





    public function queryforsaleAmountbycustomerReport($result_type)
    {
        $sql = " SELECT dbo.merchant.mrname AS detail,";
        if ($result_type == "value")
            $sql = $sql . "SUM(ISNULL(dbo.monthlysaledetail.month1, 0)) AS month1, SUM(ISNULL(dbo.monthlysaledetail.month2, 0)) AS month2,SUM(ISNULL(dbo.monthlysaledetail.month3, 0)) AS month3,SUM(ISNULL(dbo.monthlysaledetail.month4, 0)) AS month4, SUM(ISNULL(dbo.monthlysaledetail.month5, 0)) AS month5, SUM(ISNULL(dbo.monthlysaledetail.month6, 0)) AS month6,SUM(ISNULL(dbo.monthlysaledetail.month7, 0)) AS month7, SUM(ISNULL(dbo.monthlysaledetail.month8, 0)) AS month8,SUM(ISNULL(dbo.monthlysaledetail.month9, 0)) AS month9,SUM(ISNULL(dbo.monthlysaledetail.month10, 0)) AS month10,SUM(ISNULL(dbo.monthlysaledetail.month11, 0)) AS month11,SUM(ISNULL(dbo.monthlysaledetail.month12, 0)) AS month12,SUM(ISNULL(dbo.monthlysaledetail.tmonth, 0)) AS total";
        else
            $sql = $sql . "case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month1, 0))/cast(SUM(monthlysaledetail.tmonth) as float))*100),2) end As month1,    case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month2, 0))  /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month2,  case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month3, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month3,case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month4, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month4, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month5, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month5,case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month6, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month6,case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month7, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month7, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month8, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month8, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month9, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month9,  case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month10, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month10, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month11, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month11,case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month12, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month12,SUM(monthlysaledetail.tmonth) As total";

        $sql = $sql . " From  dbo.monthlysaledetail RIGHT OUTER JOIN dbo.merchant ON dbo.monthlysaledetail.mrname = dbo.merchant.mrname   WHERE        (dbo.merchant.mrname <> '') and  (dbo.merchant.mrname IS NOT NULL)";
        return $sql;
    }

    public function queryForsaleQuantitybycustomertReport($result_type)
    {
        $sql = "SELECT  dbo.merchant.mrname AS detail,";
        if ($result_type == "value")
            $sql = $sql . " SUM(ISNULL(dbo.monthlysaledetail.carton1, 0)) AS month1, SUM(ISNULL(dbo.monthlysaledetail.carton2, 0)) AS month2, SUM(ISNULL(dbo.monthlysaledetail.carton3, 0)) AS month3, SUM(ISNULL(dbo.monthlysaledetail.carton4, 0)) AS month4, SUM(ISNULL(dbo.monthlysaledetail.carton5, 0)) AS month5,SUM(ISNULL(dbo.monthlysaledetail.carton6, 0)) AS month6,SUM(ISNULL(dbo.monthlysaledetail.carton7, 0)) AS month7, SUM(ISNULL(dbo.monthlysaledetail.carton8, 0)) AS month8, SUM(ISNULL(dbo.monthlysaledetail.carton9, 0)) AS month9, SUM(ISNULL(dbo.monthlysaledetail.carton10, 0)) AS month10,SUM(ISNULL(dbo.monthlysaledetail.carton11, 0)) AS month11, SUM(ISNULL(dbo.monthlysaledetail.carton12, 0)) AS month12,SUM(ISNULL(dbo.monthlysaledetail.tcarton, 0)) AS total";
        else
            $sql = $sql . " case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton1, 0))/cast(SUM(monthlysaledetail.tcarton) as float))*100),2) end As month1, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton2, 0)) / cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month2,case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton3, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month3,  case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton4, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month4, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton5, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month5, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton6, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month6, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton7, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month7, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton8, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month8, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton9, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month9, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton10, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month10, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton11, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month11, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton12, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month12,SUM(monthlysaledetail.tcarton) As total";

        $sql = $sql . " From  dbo.monthlysaledetail RIGHT OUTER JOIN  dbo.merchant ON dbo.monthlysaledetail.mrname = dbo.merchant.mrname  WHERE  (dbo.merchant.mrname <> '') and   (dbo.merchant.mrname IS NOT NULL)";
        return $sql;
    }

    public function queryforsaleAmountbyproductReport($result_type)
    {
        $query = " SELECT monthlysaledetail.pname as detail,";
        if ($result_type == "value")
            $query = $query . "SUM(ISNULL(monthlysaledetail.month1, 0)) As month1,SUM(ISNULL(monthlysaledetail.month2, 0)) As month2,SUM(ISNULL(monthlysaledetail.month3, 0)) As month3, SUM(ISNULL(monthlysaledetail.month4, 0)) As month4,SUM(ISNULL(monthlysaledetail.month5, 0)) AS month5, SUM(ISNULL(monthlysaledetail.month6, 0)) AS month6, SUM(ISNULL(monthlysaledetail.month7, 0)) AS month7, SUM(ISNULL(monthlysaledetail.month8, 0)) AS month8,SUM(ISNULL(monthlysaledetail.month9, 0)) As month9,SUM(ISNULL(monthlysaledetail.month10, 0)) As month10, SUM(ISNULL(monthlysaledetail.month11, 0)) As month11,SUM(ISNULL(monthlysaledetail.month12, 0)) As month12, SUM(monthlysaledetail.tmonth) As total";
        else
            $query = $query . "case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month1, 0))/cast(SUM(monthlysaledetail.tmonth) as float))*100),2) end As month1, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month2, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month2,  case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month3, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month3, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month4, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month4, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month5, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month5, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month6, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month6, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month7, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month7,  case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month8, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month8, case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month9, 0)) /cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month9,case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month10, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month10,  case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month11, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month11 , case when SUM(monthlysaledetail.tmonth)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.month12, 0)) / cast(SUM(monthlysaledetail.tmonth) as float)) * 100), 2) end As month12, SUM(monthlysaledetail.tmonth) As total";

        $query = $query . " From dbo.monthlysaledetail AS monthlysaledetail LEFT OUTER JOIN dbo.whouse ON monthlysaledetail.pname = dbo.whouse.pname RIGHT OUTER JOIN dbo.merchant ON monthlysaledetail.mrname = dbo.merchant.mrname WHERE (dbo.merchant.mrname <> '') and (monthlysaledetail.pname IS NOT NULL)";
        return $query;
    }

    public function queryForsaleQuantitybyproductReport($result_type)
    {
        $query = " SELECT monthlysaledetail.pname as detail,";
        if ($result_type == "value")
            $query  = $query  . "SUM(ISNULL(monthlysaledetail.carton1, 0)) AS month1,SUM(ISNULL(monthlysaledetail.carton2, 0)) AS month2,SUM(ISNULL(monthlysaledetail.carton3, 0)) AS month3,SUM(ISNULL(monthlysaledetail.carton4, 0)) AS month4, SUM(ISNULL(monthlysaledetail.carton5, 0)) AS month5,SUM(ISNULL(monthlysaledetail.carton6, 0)) AS month6,   SUM(ISNULL(monthlysaledetail.carton7, 0)) AS month7, SUM(ISNULL(monthlysaledetail.carton8, 0)) AS month8,SUM(ISNULL(monthlysaledetail.carton9, 0)) AS month9,  SUM(ISNULL(monthlysaledetail.carton10, 0)) AS month10, SUM(ISNULL(monthlysaledetail.carton11, 0)) AS month11,SUM(ISNULL(monthlysaledetail.carton12, 0)) AS month12,   SUM(monthlysaledetail.tcarton) AS total";
        else
            $query  = $query  . "case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton1, 0))/cast(SUM(monthlysaledetail.tcarton) as float))*100),2) end As month1, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton2, 0)) / cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month2,case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton3, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month3, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton4, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month4,case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton5, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month5,  case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton6, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month6, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton7, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month7, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton8, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month8,  case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton9, 0)) /cast( SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month9, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton10, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month10, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton11, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month11, case when SUM(monthlysaledetail.tcarton)=0 then 0 else round(((SUM(ISNULL(monthlysaledetail.carton12, 0)) / cast(SUM(monthlysaledetail.tcarton) as float)) * 100), 2) end As month12, SUM(monthlysaledetail.tcarton) As total";

        $query  = $query  . " From dbo.monthlysaledetail AS monthlysaledetail LEFT OUTER JOIN dbo.whouse ON monthlysaledetail.pname = dbo.whouse.pname RIGHT OUTER JOIN dbo.merchant ON monthlysaledetail.mrname = dbo.merchant.mrname WHERE (dbo.merchant.mrname <> '') and (monthlysaledetail.pname IS NOT NULL)";
        return $query;
    }



    public function queryForYearlyBuyAmountReport($result_type)
    {
    }

    public function queryForYearlyBuyQuantityReport($result_type)
    {
    }



    public function getBuySellReportShort($data)
    {
        $ie = isset($data['reportType']) ? $data['reportType'] : "";
        $fromDate = isset($data['fromDate']) ? $data['reportType'] : "";
        $toDate = isset($data['toDate']) ? $data['reportType'] : "";

        $query = "SELECT pclass, manufacturer, pname, pbarcode, SUM(carton) AS Quantity, SUM(bonis) AS Bonus, pprice AS Price, SUM(carton) * pprice AS Total FROM dbo.adata ";

        $filterQuery = "where ie = '" . $ie . "' ";
        $filterQuery = $filterQuery . " and (date1 >= '" . $fromDate . "' and date1 <= '" . $toDate . "') ";

        if (isset($data['barcode'])) {
            $filterQuery = $filterQuery . " and pbarcode = '" . $data['barcode'] . "' ";
        }
        if (isset($data['pname'])) {
            $filterQuery = $filterQuery . " and pname = '" . $data['pname'] . "' ";
        }
        if (isset($data['pclass'])) {
            $filterQuery = $filterQuery . " and pclass = '" . $data['pclass'] . "' ";
        }
        if (isset($data['manufacturer'])) {
            $filterQuery = $filterQuery . " and manufacturer = '" . $data['manufacturer'] . "' ";
        }
        if (isset($data['customerName'])) {
            $filterQuery = $filterQuery . " and mrname = '" . $data['mrname'] . "' ";
        }
        if (isset($data['scientificn'])) {
            $filterQuery = $filterQuery . " and scientificn = '" . $data['scientificn'] . "' ";
        }
        $groupBy = "GROUP BY pname, pprice, manufacturer, pclass, pbarcode ";
        $having = "HAVING (pname IS NOT NULL) ";

        return $query = $query . $filterQuery . $groupBy . $having;
    }


    public function getBuySellReportDetailed($data)
    {

        $ie = isset($data['reportType']) ? $data['reportType'] : "";
        $fromDate = isset($data['fromDate']) ? $data['reportType'] : "";
        $toDate = isset($data['toDate']) ? $data['reportType'] : "";

        $query = "SELECT manufacturer, pclass, itemno, date1, mrname, pname, pbarcode, carton as Quantity, bonis AS Bonus, pprice as Price, carton * pprice AS Total FROM dbo.adata ";

        $filterQuery = "where ie = '" . $ie . "' ";
        $filterQuery = $filterQuery . " and (date1 >= '" . $fromDate . "' and date1 <= '" . $toDate . "') ";

        if (isset($data['barcode'])) {
            $filterQuery = $filterQuery . " and pbarcode = '" . $data['barcode'] . "' ";
        }
        if (isset($data['pname'])) {
            $filterQuery = $filterQuery . " and pname = '" . $data['pname'] . "' ";
        }
        if (isset($data['pclass'])) {
            $filterQuery = $filterQuery . " and pclass = '" . $data['pclass'] . "' ";
        }
        if (isset($data['manufacturer'])) {
            $filterQuery = $filterQuery . " and manufacturer = '" . $data['manufacturer'] . "' ";
        }
        if (isset($data['customerName'])) {
            $filterQuery = $filterQuery . " and mrname = '" . $data['mrname'] . "' ";
        }
        if (isset($data['scientificn'])) {
            $filterQuery = $filterQuery . " and scientificn = '" . $data['scientificn'] . "' ";
        }


        return $query = $query . $filterQuery;
    }
}
