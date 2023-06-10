<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesAttendance;
use DataTables;

class SalesAttendanceController extends Controller
{
    public function index(){
        return view('admin.absensi-sales.index');
    }

    public function detail($id){
        return SalesAttendance::with('sales')->find($id);
    }

    public function datatables(Request $request){
        $data = SalesAttendance::with('sales');
        if(isset($request->tanggal_mulai)){
            $data->whereBetween('date_start',[$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $data->orderBy('date_start', 'desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
}
