<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DataTables;

use App\Models\JasaPengiriman;

class JasaPengirimanController extends Controller
{
    public function index(){
        return view('admin.jasa-pengiriman.index');
    }

    public function datatables(){
        $data = JasaPengiriman::orderBy('id','desc')->orderBy('status', 'asc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function action($id, $status){
        JasaPengiriman::where('id', $id)->update(['status' => $status]);

        if($status == 1){
            $message = 'Data berhasil diaktifkan';
        }else{
            $message = 'Data berhasil dinon-aktifkan';
        }

        return [
            'status' => 200,
            'message' => $message
        ];
    }
}
