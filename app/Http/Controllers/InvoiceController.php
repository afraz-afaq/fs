<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{


    /**
     * @OA\Get(
     *     path="/invoice",
     *     tags={"Invoice Table List"},
     *     summary="Returns the invoices",
     *     description="Return the list of Invoices.",
     *     operationId="index",
     *    @OA\Parameter(
     *     name="customer_name",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="date",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Parameter(
     *     name="sort_order",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *  @OA\Parameter(
     *     name="sort_type",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
  
     *     @OA\Response(
     *         response=200,
     *          description="All Invoices",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [Invoice Object, Invoice Object], 'message':'All Invoices'}")
     *        )
     *     ),
     * )
     */

    public function index()
    {

        $query = "SELECT TOP (100) PERCENT itemno, mrname, date1, SUM(total) AS total, isdelete 
        FROM (SELECT itemno, mrname, date1, SUM(carton) * SUM(pprice) - (SUM(arrivel) + SUM(discount)) 
        AS total, isdelete 
        FROM ie
        where ie = 'sale'
        GROUP BY itemno, mrname, date1, ie2, isdelete HAVING (ie2 IS NULL) OR (ie2 = N'') OR (ie2 = N'voucher')) AS derivedtbl_1";

        if (isset($_GET['customer_name']) && isset($_GET['date']))
            $query .= " where mrname = '" . $_GET['customer_name'] . "' And date1 = '" . $_GET['date'] . "'";
        else if (isset($_GET['customer_name']))
            $query .= " where mrname = '" . $_GET['customer_name'] . "'";
        else if (isset($_GET['date']))
            $query .= " where date1 = '" . $_GET['date'] . "'";

        $ending_of_query = " GROUP BY itemno, mrname, date1, isdelete";

        if (isset($_GET['sort_order']))
            $ending_of_query .= " order by " . $_GET['sort_order'] . " " . $_GET['sort_type'];

        $ies = DB::select($query . $ending_of_query);


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['invoices' => $ies], 'message' => "All Invoices"]
        ], 200);
    }



    /**
     * @OA\Get(
     *     path="/invoice/view",
     *     tags={"Invoice View"},
     *     summary="Returns the invoice",
     *     description="Returns the invoice.",
     *     operationId="view",
     * 
     *  @OA\Parameter(
     *     name="ie",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *      *  @OA\Parameter(
     *     name="isDelete",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *      *  @OA\Parameter(
     *     name="itemno",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Invoice",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : {invoice Object}, 'message':'Invoice retrieved'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */
    public function view()
    {

        $ie = $_GET['ie'];
        $itemo = $_GET['itemno'];
        $isDelete = $_GET['isDelete'];

        $ie = DB::select("Select id, ie, itemno, mrname, date1, pname, carton, pprice, bonis, username 
        from dbo.ie 
        where ie = '$ie' and itemno = $itemo and isdelete = $isDelete and ie2 is NULL");

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['invoice' => $ie], 'message' => "Invoice Retreived"]
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/invoice/product-delete",
     *     tags={"Invoice Product Delete"},
     *     summary="Deletes the invoice product",
     *     description="Deletes the invoice product",
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
     *          description="Invoice Product Deleted.",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : {flag}, 'message':'Invoice Product Deleted.'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */
    public function deleteProduct()
    {

        $isDelete = $_GET['id'];
        $flag = DB::update("UPDATE dbo.ie set isdelete = 1 where id = $isDelete");

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Invoice Product Deleted."]
        ], 200);
    }




    /**
     *@OA\Schema(
     *  schema="InvoiceUpdate",
     *  title="Invoice Update model",
     *  description="Model for Updating Invoice",
     *  @OA\Property(
     *     property="ie",
     *     type="String",
     *     description="ie",
     *      example="sale"
     *  ),
     * 
     *  @OA\Property(
     *     property="mrname",
     *     type="String",
     *     description="Customer name",
     *      example="Foo"
     *  ),
     * 
     *   @OA\Property(
     *     property="arrivel",
     *     type="String",
     *     description="Arrivel",
     *      example="qwqw"
     *  ),
     * 
     *  @OA\Property(
     *     property="itemno",
     *     type="string",
     *     description="item no",
     *      example="121"
     *  ),
     * 
     *  @OA\Property(
     *     property="date",
     *     type="string",
     *     description="date of invoice",
     *      example="2020-09-09"
     *  ),
     * 
     *    @OA\Property(
     *     property="discount",
     *     type="string",
     *     description="Discount",
     *      example="1"
     *  ),
     *  @OA\Property(
     *     property="comment",
     *     type="string",
     *     description="Comment",
     *      example="YOYO"
     *  )
     * 
     *)
     */


    /**
     * @OA\Post(
     *     path="/invoice/update",
     *     tags={"Invoice update"},
     *     summary="Invoice update",
     *     description="Invoice update",
     *     operationId="updateProduct",
     *    @OA\RequestBody(
     *         description="update invoice object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceUpdate")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="validation fails"
     *     ),
     *  @OA\Response(
     *         response="200",
     *         description="Invoice Updated."
     *     ),
     * )
     */
    public function updateProduct()
    {

        $ie = $_POST['ie'];
        $mrname = $_POST['mrname'];
        $date = $_POST['date'];
        $arrivel = $_POST['arrivel'];
        $discount = $_POST['discount'];
        $comment = $_POST['comment'];
        $itemno = $_POST['itemno'];

        $flag = DB::update("UPDATE dbo.ie SET mrname = '$mrname', date1 = '$date', arrivel = '$arrivel', discount = '$discount', comment = '$comment' where ie='$ie' and ie2='voucher' and itemno= $itemno");

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Invoice Updated."]
        ], 200);
    }

    public function getInvoiceNumber()
    {

        $ie = $_GET['ie'];
        $username = $_GET['username'];

        $table = "";

        switch ($ie) {
            case "fwhouse":
                $table = "fwhouseitems";
                break;
            case "sale":
                $table = "saleitems";
                break;
            case "fdmerchant":
                $table = "merchantfditems";
                break;
            case "voucher":
                $table = "voucheritems";
                break;
            case "buy":
                $table = "buyitems";
                break;
            case "payvoucher":
                $table = "payvoucheritems";
                break;
            case "draftsale":
                $table = "draftsaleitems";
                break;
            case "returnsale":
                $table = "returnsaleitems";
                break;
        }
    }
}
