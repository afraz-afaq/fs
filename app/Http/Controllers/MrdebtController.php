<?php

namespace App\Http\Controllers;

use App\Models\Mrdebt;
use Illuminate\Http\Request;

class MrdebtController extends Controller
{
    public function index(){

        $debts_list = Mrdebt::select(['mrname','mrdebt','mrtype','governer','city']);

        if(isset($_GET['type']))
            $debts_list = $debts_list->where('mrtype',$_GET['type']);
        if(isset($_GET['address']))
            $debts_list = $debts_list->where('governer',$_GET['address']);
        if(isset($_GET['order_by']) && isset($_GET['sort_by'])){
            $debts_list = $debts_list->orderBy($_GET['order_by'],$_GET['sort_by']);
        }else if(isset($_GET['order_by'])){
            $debts_list = $debts_list->orderBy($_GET['order_by'],'asc');
        }

        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['debts' => $debts_list->get(), 'sum' => $debts_list->sum('mrdebt'), 'count' => $debts_list->count()], 'message' => "All mrdepts"]
        ], 200);
    }
}
