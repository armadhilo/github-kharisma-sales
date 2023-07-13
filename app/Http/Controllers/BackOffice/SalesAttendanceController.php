<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesAttendanceRequest;
use Illuminate\Http\Request;
use App\Models\SalesAttendance;
use DataTables;

class SalesAttendanceController extends Controller
{
    public function index(){
        return view('admin.absensi-sales.index');
    }

    public function edit(){
        return view('admin.absensi-sales.edit');
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
                    ->addColumn('badge_custom',function($item) {
                        if($item->date_end == null){
                            return '<span class="badge badge-pill badge-warning">still working</span>';
                        }else{
                            return '<span class="badge badge-pill badge-success">finish working</span>';
                        }
                    })
                    ->rawColumns(['badge_custom'])
                    ->make(true);
    }

    public function update(SalesAttendanceRequest $request){
        SalesAttendance::where('id', $request->id)->update($request->all() );

        return [
            'status' => 200, // SUCCESS
            'message' => 'Data berhasil disimpan',
        ];
    }
}
