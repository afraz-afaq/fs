<?php

use App\Http\Controllers\BudgetReportController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MrdebtController;
use App\Http\Controllers\MrtypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WhouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations\Get;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('user/login-inline',function(Request $request)    {
    $validator = Validator::make($request->all(), [
        'username' => 'required',
        'password' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'statusCode' => 200,
            'data' => $validator->messages(),
        ], 200);
    }
    $requestData = $request->all();
    $user = User::where('uname', $requestData['username'])
        ->where('upass', $requestData['password'])
        ->where('active',1)
        ->where('ios',1)
        ->where('cislocked',0)
        ->where('cisdelete',0)
        ->first();
    if ($user)
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => $user,
        ], 200);
    else
    return response()->json([
        'status' => false,
        'statusCode' => 200,
        'data' => "User does not exists",
    ], 200);
});

Route::POST('user/login', [UserController::class, 'login']);
Route::GET('list/{id}',[FilterController::class,'getList']);
Route::POST('user/signup', [UserController::class, 'signup']);
Route::GET('user/save-token', [UserController::class, 'savedeviceToken']);
Route::POST('user/reset-password', [UserController::class, 'resetPassword']);

Route::GET('whouse',[WhouseController::class, 'index']);
Route::GET('whouse/{id}',[WhouseController::class, 'view']);

Route::GET('product',[ProductController::class, 'index']);
Route::POST('product/save',[ProductController::class, 'save']);
Route::GET('product/{id}',[ProductController::class, 'view']);
Route::GET('product/{id}/status/{status}',[ProductController::class, 'changeStatus']);

Route::GET('mrtype',[MrtypeController::class, 'index']);
Route::GET('mrdebt',[MrdebtController::class, 'index']);

Route::GET('invoice',[InvoiceController::class, 'index']);
Route::GET('invoice/view',[InvoiceController::class, 'view']);
Route::GET('invoice/product-delete',[InvoiceController::class, 'deleteProduct']);
Route::POST('invoice/update',[InvoiceController::class, 'updateProduct']);
Route::GET('invoice/get-itemno',[InvoiceController::class, 'getInvoiceNumber']);
Route::POST('invoice/save',[InvoiceController::class, 'saveInvoice']);
Route::POST('invoice/save-product',[InvoiceController::class, 'saveInvoiceProduct']);
Route::GET('invoice/delete',[InvoiceController::class, 'deleteInvoice']);

Route::GET('voucher',[VoucherController::class, 'index']);
Route::GET('voucher/pay',[VoucherController::class, 'pay']);
Route::GET('voucher/delete',[VoucherController::class, 'deleteVoucher']);

Route::GET('notification',[NotificationController::class, 'index']);

Route::GET('report/budget',[ReportController::class, 'budgetReport']);
Route::GET('report/chart',[ReportController::class, 'chartReport']);
Route::GET('report/yearly',[ReportController::class, 'getBuySellYearlyReport']);
Route::GET('report/buy-sell',[ReportController::class, 'buySellReport']);

Route::GET('merchant',[MerchantController::class, 'index']);