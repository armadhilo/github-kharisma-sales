<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KodeVoucher;
use DataTables;
use App\Helpers\Convert;
use App\Http\Requests\KodeVoucherRequest;
use Illuminate\Support\Facades\Validator;   

class KodeVoucherController extends Controller
{
    public function index(){
        return view('admin.kode-voucher.index');
    }

    public function detail($id){
        return KodeVoucher::with('user')->find($id);
    }

    public function datatables(){
        $data = KodeVoucher::with('user')->orderBy('id','desc')->orderBy('status', 'asc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    // public function datatables_history(request $request){
    //     $data = Transaksi::with('user.tipe_customer')->where(['kode_voucher' => $request->kode_voucher, 'status' => '4'])->get();

    //     return DataTables::of($data)
    //                 ->addIndexColumn()
    //                 ->addColumn('total_harga_belanja', function($item) {
    //                     return $item->total_harga - $item->harga_pengiriman;
    //                 })
    //                 ->make(true);
    // }

    public function store_update(KodeVoucherRequest $request){

        if($request->tanggal_mulai > $request->tanggal_selesai){
            return [
                'status' => 300,
                'message' => 'Oops! tanggal kurang sesuai harap cek terlebih dahulu'
            ];
        }

        KodeVoucher::updateOrCreate(['id' => $request->id],$request->all() );

        return [
            'status' => 200, // SUCCESS
            'message' => 'Data berhasil disimpan'
        ];
    }

    public function delete($id){
        $delete = KodeVoucher::find($id);

        if($delete <> ""){
            $delete->delete();
            return [
                'status' => 200,
                'message' => 'Data berhasil dihapus'
            ];
        }

        return [
            'status' => 300,
            'message' => 'Data tidak ditemukan'
        ];
    }
}
