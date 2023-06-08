<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use DataTables;

class KunjunganSalesController extends Controller
{
    public function index(){
        return view('admin.kunjungan-sales.index');
    }

    public function detail($id){
        return Kunjungan::with('sales', 'pelanggan')->find($id);
    }

    public function datatables(Request $request){
        $data = Kunjungan::with('sales', 'pelanggan');
        if(isset($request->tanggal_mulai)){
            $data->whereBetween('date',[$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $data->orderBy('date', 'desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
}
