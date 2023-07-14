<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\SalesLog;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;

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

    public function datatables(Request $request){
        $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay() ?? Carbon::now()->startOfDay();
        $tanggalAkhir = Carbon::parse($request->tanggal_selesai)->endOfDay() ?? Carbon::now()->endOfDay();
        $data = User::where('type', 1)->whereHas('sales_log', function($query) use ($tanggalMulai, $tanggalAkhir){
            $query->whereBetween('created_at',[$tanggalMulai, $tanggalAkhir]);
        })->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
}
