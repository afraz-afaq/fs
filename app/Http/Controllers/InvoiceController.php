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
     *     description="Return the list of Invoices. Sort Order: itemno, mrname, date1, total. Sort Type: asc, desc",
     *     operationId="index",
     *   @OA\Parameter(
     *     name="ie",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
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
     * @OA\Parameter(
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
        $ie = isset($_GET['ie']) ? $_GET['ie'] : 'sale';
        $query = "SELECT TOP (100) PERCENT  itemno, mrname, date1, SUM(total) AS total, isdelete 
        FROM (SELECT  itemno, mrname, date1, SUM(carton) * SUM(pprice) - (SUM(arrivel) + SUM(discount)) 
        AS total, isdelete 
        FROM ie
        where ie = '$ie'
        GROUP BY  itemno, mrname, date1, ie2, isdelete HAVING (ie2 IS NULL) OR (ie2 = N'') OR (ie2 = N'voucher')) AS derivedtbl_1";

        $mrname = "";
        if (isset($_GET['usertype'])) {
            if (isset($_GET['usertype']) == "customer")
                $mrname = $_GET['company_name'];
        }
        else
            $mrname = isset($_GET['customer_name']) ? $_GET['customer_name']: "";

        if ($mrname && $mrname != "" && isset($_GET['date']))
            $query .= " where mrname = '" . $mrname . "' And date1 = '" . $_GET['date'] . "'";
        else if (isset($_GET['searchbox']) && isset($_GET['date']))
            $query .= " where mrname like '%" . $_GET['searchbox'] . "%' And date1 = '" . $_GET['date'] . "'";
        else if ($mrname && $mrname != "")
            $query .= " where mrname = '" . $mrname . "'";
        else if (isset($_GET['searchbox']))
            $query .= " where mrname like '%" . $_GET['searchbox'] . "%'";
        else if (isset($_GET['date']))
            $query .= " where date1 = '" . $_GET['date'] . "'";

        $ending_of_query = " GROUP BY  itemno, mrname, date1, isdelete";

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
        where ie = '$ie' and itemno = '$itemo' and isdelete = '$isDelete' and ie2 is NULL");

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
     * @OA\Get(
     *     path="/invoice/delete",
     *     tags={"Invoice Delete"},
     *     summary="Deletes the invoice",
     *     description="Deletes the invoice",
     *     operationId="deleteInvoice",
     * 
     *  @OA\Parameter(
     *     name="itemno",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     * 
     *@OA\Parameter(
     *     name="ie",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Invoice Deleted.",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : {flag}, 'message':'Invoice Deleted.'}")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="No such record exists",
     *     ),
     * )
     */
    public function deleteInvoice()
    {

        $isDelete = $_GET['itemno'];
        $ie = $_GET['ie'];
        $flag = DB::update("UPDATE dbo.ie set isdelete = 1 where itemno = '$isDelete' and ie = '$ie'");

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Invoice Deleted."]
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



    /**
     * @OA\Get(
     *     path="/invoice/get-itemno",
     *     tags={"Invoice Get Item No"},
     *     summary="Returns the itemno",
     *     description="Return the last itemno to make new invoice.",
     *     operationId="getInvoiceNumber",
     *    @OA\Parameter(
     *     name="ie",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *    @OA\Parameter(
     *     name="username",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     ),
     * ),
     *     @OA\Response(
     *         response=200,
     *          description="Item No.",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="true"),
     *       @OA\Property(property="statusCode", type="integer", example="200"),
     *       @OA\Property(property="data", type="string", example="{'data' : [itemno : 12], 'message':'Item No.'}")
     *        )
     *     ),
     * )
     */
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

        DB::insert("INSERT INTO $table (username) VALUES ('$username' )");
        $record = DB::select("select top 1 itemno from $table order by itemno DESC");


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['itemno' => $record[0]->itemno], 'message' => "Item No.."]
        ], 200);
    }




    /**
     *@OA\Schema(
     *  schema="InvoiceSave",
     *  title="Invoice Save model",
     *  description="Model for saving Invoice",
     *  @OA\Property(
     *     property="ie",
     *     type="String",
     *     description="ie",
     *      example="sale"
     *  ),
     *      *  @OA\Property(
     *     property="ie2",
     *     type="String",
     *     description="ie2",
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
     *     property="username",
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
     *)
     */


    /**
     * @OA\Post(
     *     path="/invoice/save",
     *     tags={"Invoice Save"},
     *     summary="Invoice Save",
     *     description="Invoice Save",
     *     operationId="saveInvoice",
     *    @OA\RequestBody(
     *         description="save invoice object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceSave")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="validation fails"
     *     ),
     *  @OA\Response(
     *         response="200",
     *         description="Invoice Saved."
     *     ),
     * )
     */

    public function saveInvoice(Request $request)
    {

        $data = $request->all();
        $ie = $data['ie'];
        $ie2 = isset($data['ie2']) ? $data['ie2'] : '';
        $date = $data['date'];
        $mrname = $data['mrname'];
        $username = $data['username'];
        $itemno = $data['itemno'];

        $flag = DB::insert("INSERT INTO dbo.ie (ie,ie2,itemno,mrname,date1,username) VALUES (?, ?, ?, ?, ?, ?)", [$ie, $ie2, $itemno, $mrname, $date, $username]);
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Invoice Saved."]
        ], 200);
    }





    /**
     *@OA\Schema(
     *  schema="InvoiceProductSave",
     *  title="Invoice Product Save model",
     *  description="Model for saving Product Invoice",
     *  @OA\Property(
     *     property="ie",
     *     type="String",
     *     description="ie",
     *      example="sale"
     *  ),
     *      *  @OA\Property(
     *     property="pname",
     *     type="String",
     *     description="pname",
     *      example="Flower"
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
     *     property="username",
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
     *   @OA\Property(
     *     property="pprice",
     *     type="string",
     *     description="pprice",
     *      example="20"
     *  ),
     *    @OA\Property(
     *     property="bonis",
     *     type="string",
     *     description="bonis",
     *      example="2"
     *  ),
     *    @OA\Property(
     *     property="carton",
     *     type="string",
     *     description="carton",
     *      example="2"
     *  ),
     * 
     *)
     */


    /**
     * @OA\Post(
     *     path="/invoice/save-product",
     *     tags={"Invoice Product Save"},
     *     summary="Invoice Product Save",
     *     description="Invoice Product Save",
     *     operationId="saveInvoiceProduct",
     *    @OA\RequestBody(
     *         description="save invoice Product object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceProductSave")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="validation fails"
     *     ),
     *  @OA\Response(
     *         response="200",
     *         description="Invoice Product Saved."
     *     ),
     * )
     */


    public function saveInvoiceProduct(Request $request)
    {

        $data = $request->all();

        $ie = $data['ie'];
        $pname = $data['pname'];
        $date = $data['date'];
        $mrname = $data['mrname'];
        $username = $data['username'];
        $itemno = $data['itemno'];
        $bonis = isset($data['bonis']) ? $data['bonis'] : null;
        $pprice = isset($data['pprice']) ? $data['pprice'] : null;
        $carton = isset($data['carton']) ? $data['carton'] : null;


        $flag = DB::insert(
            "INSERT INTO dbo.ie (ie, itemno, mrname, date1, pname, carton, pprice, bonis, username)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$ie, $itemno, $mrname, $date, $pname, $carton, $pprice, $bonis, $username]
        );
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['flag' => $flag], 'message' => "Invoice Product Saved."]
        ], 200);
    }
}
