<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use App\Http\Requests\EditKunjunganSalesRequest;
use DataTables;
use \Carbon\Carbon;

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

        $data->orderBy('date', 'desc')->groupBy('sales_id')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }
    
    public function datatables_edit(){
        try
        {
            $query = Kunjungan::query()
                ->leftJoin('users as sales', 'sales.pelanggan_id', '=', 'kunjungan.sales_id')
                ->leftJoin('users as pelanggan', 'pelanggan.pelanggan_id', '=', 'kunjungan.pelanggan_id')
                ->select([
                    'kunjungan.*',
                    'sales.nama as sales_nama',
                    'pelanggan.nama as pelanggan_nama',
                ])
                ->orderBy('kunjungan.date', 'desc');

            return datatables()->of($query)
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Throwable $e)
        {
            \Log::error('Datatables Kunjungan Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // public function datatables_edit(){
    //     $data = Kunjungan::with('sales', 'pelanggan')->orderBy('date', 'desc')->get();
    //     return DataTables::of($data)
    //                 ->addIndexColumn()
    //                 ->make(true);
    // }

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

        $concat_datetime_start = $request->date ." ". $request->time_start;
        $concat_datetime_end = $request->date ." ". $request->time_end;
        $kunjungan_start = Carbon::parse("$concat_datetime_start");

        //mengambil selisih menit jika true maka terlambat
        $diff = $kunjungan_start->diffInMinutes(Carbon::parse("$concat_datetime_end"), false);

        $request->merge(['total_time' => $diff]);
        Kunjungan::where('id', $request->id)->update($request->all() );
        
        return [
            'status' => 200, // SUCCESS
            'message' => 'Data berhasil disimpan',
        ];
    }

}
