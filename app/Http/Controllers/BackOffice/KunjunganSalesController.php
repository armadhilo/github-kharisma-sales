<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use App\Http\Requests\EditKunjunganSalesRequest;
use DataTables;

class KunjunganSalesController extends Controller
{
    public function index(){
        return view('admin.kunjungan-sales.index');
    }

    public function edit(){
        return view('admin.kunjungan-sales.edit');
    }

    public function detail($id){
        return Kunjungan::with('sales', 'pelanggan')->find($id);
    }
    
    public function datatables(Request $request){
        $data = Kunjungan::with('sales', 'pelanggan');
        if(isset($request->tanggal_mulai)){
            $data->whereBetween('date',[$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $data->orderBy('date', 'asc')->groupBy('sales_id')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
                
    public function datatables_edit(){
        $data = Kunjungan::with('sales', 'pelanggan')->orderBy('date', 'asc')->get();
        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function datatables_detail(Request $request){
        $data = Kunjungan::with('sales', 'pelanggan')->where('sales_id', $request->id);
        if(isset($request->tanggal_mulai)){
            $data->whereBetween('date',[$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $data->orderBy('date', 'asc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function update(Request $request){
        Kunjungan::where('id', $request->id)->update($request->all() );
        return [
            'status' => 200, // SUCCESS
            'message' => 'Data berhasil disimpan',
        ];
    }

}
