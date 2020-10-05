<?php

namespace App\Http\Controllers;

use App\Models\Mrtype;
use Illuminate\Http\Request;

class MrtypeController extends Controller
{
    public function index(){
        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'data' => ['data' => ['mrtypes' => Mrtype::get()], 'message' => "All mrtypes"]
        ], 200);
    }
}
