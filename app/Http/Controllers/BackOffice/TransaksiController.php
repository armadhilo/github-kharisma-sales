<?php

namespace App\Http\Controllers\BackOffice;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use App\Models\AlamatPengiriman;
use App\Models\AsalPengiriman;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use DataTables;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = SalesOrder::has('user')->all();
        $data['count_semua'] = $transaksi->count();
        $data['count_konfirmasi_admin'] = $transaksi->where('status', 'draft')->count();
        $data['count_sale'] = $transaksi->where('status', 'sale')->count();
        $data['count_done'] = $transaksi->where('status', 'done')->count();
        $data['count_cancel'] = $transaksi->where('status', 'cancel')->count();

        return view('admin.transaksi.index', $data);
    }

    public function detail($uuid){
        $data['data'] = SalesOrder::with('user', 'sales_order_product.product_original')->where('uuid', $uuid)->first();
        $data['alamat_pengiriman'] = AlamatPengiriman::where('user_id', $data['data']->user_id)->first();
        return view('admin.transaksi.transaksi-detail', $data);
    }

    public function datatables(Request $request){
        $data = SalesOrder::with('user')->has('user')->latest()->get();

        if($request->status == 'konfirmasi_admin'){
            $data = $data->where('status', 'draft');
        }elseif($request->status == 'diproses'){
            $data = $data->where('status', 'sale');
        }elseif($request->status == 'selesai'){
            $data = $data->where('status', 'done');
        }elseif($request->status == 'cancel'){
            $data = $data->where('status', 'cancel');
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function cetak_invoice_pdf($uuid){
        $data['data'] = SalesOrder::with('user', 'sales_order_product.product_original')->where('uuid', $uuid)->first();
        $data['alamat_pengiriman'] = AlamatPengiriman::where('user_id', $data['data']->user_id)->first();
        return view('pdf.invoice',$data);
    }

    public function cetak_resi_pengiriman($id){

        $data['transaksi'] = SalesOrder::with('user', 'sales_order_product.product_original')->has('sales_order_product.product')->where('id', $id)->first();
        $data['alamat_pengiriman'] = AlamatPengiriman::where('user_id', $data['transaksi']->user_id)->first();
        $data['asal_pengiriman'] = AsalPengiriman::first();

        $pdf = Pdf::loadview('pdf.resi-pengiriman',$data);
        $pdf->setPaper("A4","potrait");

        return [
            'status' => 200,
            'data' => [
                'base64' => 'data:application/pdf;base64,'.base64_encode($pdf->stream()),
                'filename' => 'Resi Pengiriman Invoice ' . $data['transaksi']->no_invoice ,
            ],
        ];
    }
}
