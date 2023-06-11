<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\SalesLog;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class SalesLogController extends Controller
{
    public function index(){
        return view('admin.sales-log.index');
    }

    public function detail($id){
        $data['id'] = $id;
        return view('admin.sales-log.detail', $data);
    }

    public function detail_data($id){
        $data = SalesLog::where('sales_id', $id)->orderBy('created_at', 'asc')->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function datatables(){
        $data = User::where('type', 1)->has('sales_log')->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
}
