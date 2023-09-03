<?php

namespace App\Http\Controllers\BackOffice;

use App\Helpers\OdooApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\PelangganRequest;
use App\Models\AlamatPengiriman;
use App\Models\SalesPelanggan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use DataTables;
use DB;
use Google\Service\CloudBuild\RetryBuildRequest;
use Illuminate\Support\Facades\Route;


class PelangganController extends Controller
{

    public function index(){
        $data['jobs'] = DB::table('jobs')->count();
        $data['failed_jobs'] = DB::table('failed_jobs')->count();
        $data['total_user'] = User::count();
        return view('admin.pelanggan.index', $data);
    }

    public function add(){
        $data['sales'] = User::where('type', 1)->get();
        return view('admin.pelanggan.add', $data);
    }

    public function detail($id){
        $data['data'] =  User::where('type', 2)->find($id);
        $data['sales'] = User::where('type', 1)->get();
        $data['sales_pelanggan'] = SalesPelanggan::where('pelanggan_id', $data['data']->pelanggan_id)->get();
        return view('admin.pelanggan.add',$data);
    }

    public function datatables(){
        $data = User::where('type', 2)->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status_badge', function($item) {

                        if(!empty($item->keylog)){
                            return "<span class='badge badge-success'>active</span>";
                        }else{
                            return "<span class='badge badge-danger'>inactive</span>";
                        }

                    })
                    ->rawColumns(['status_badge'])
                    ->make(true);
    }

    public function store_update(PelangganRequest $request){

        if(substr($request->telepon, 0, 2) != '08'){
            return [
                'status' => 300,
                'message' => 'Nomer harus dimulai dari 08xxx',
            ];
        }

        $request->request->add(['provinsi_id' => '23']);
        $request->request->add(['provinsi' => 'NUSA TENGGARA TIMUR']);
        $request->request->add(['no_telepon' => $request->telepon]);
        $request->request->add(['penerima' => $request->nama]);
        $request->request->add(['kode_pos' => $request->zip]);

        if($request->has('id')){
            $user = User::find($request->id);

            //jika password masih kosong wajib isi
            if(empty($user->password)){
                return [
                    'status' => 300,
                    'message' => 'Pelanggan belum memiliki password harap setting terlebih dahulu',
                ];
            }

            //append keylog jika user_keylog masih kosong
            if(empty($user->keylog)) $request->request->add(['keylog' => Str::random(40)]);
        }

        if(!request()->has('id') || request()->has('keylog')){
            $request->request->add(['type' => 'pelanggan']);
            $request->request->add(['keylog' => Str::random(40)]);
        }

        //get kabupaten name
        // $request = Request::create('/api/kabkota/53', 'GET');
        // $response = Route::dispatch($request);
        // $collection = collect(collect($response)['original']);
        // dd($collection->where('nama', 'KAB. KUPANG')->first()['id']);

        if(request()->has('password')){
            $request->request->add(['password' =>Hash::make($request->password)]);
        }

        try {
            DB::beginTransaction();

            $data = User::updateOrCreate(['id' => $request->id],$request->all() );

            //update data on odoo
            $user = User::find($request->id);
            if(!empty($user) && $user->type == 'pelanggan'){
                $parameter['pelanggan_id'] = $user->pelanggan_id;
                $parameter['street'] = $user->street;
                $parameter['country_id'] = 100;
                $parameter['phone'] = $request->phone;
                $parameter['zip'] = $request->zip;
                $parameter['email'] = $request->email;
                $parameter['mobile'] = $request->telepon;
                $parameter['npwp'] = $request->no_npwp;
                $parameter['npwp_name'] = $request->nama_npwp;
                $parameter['id_nik'] = $request->no_ktp;
                $parameter['street'] = $request->street;
                $parameter['street2'] = $request->street2;
                $parameter['city'] = $request->city;

                $odoo_api = OdooApi::updateCustomer($parameter);
                if(!$odoo_api){
                    return [
                        'status' => 300,
                        'message' => 'Terjadi kesalahan saat syncronize dengan Odoo',
                    ];
                }
            }

            //change user_id to pelanggan_id
            $request->merge(['user_id' => $request->pelanggan_id]);
            AlamatPengiriman::updateOrCreate(['user_id' => $request->user_id],$request->all());

            if($request->has(['id', 'sales_pelanggan_id', 'pelanggan_id'])){
                SalesPelanggan::where('pelanggan_id', $request->pelanggan_id)->delete();
            }

            foreach($request->sales_pelanggan_id as $item){
                SalesPelanggan::create([
                    'sales_id' => $item,
                    'pelanggan_id' => $request->pelanggan_id
                ]);
            }

            DB::commit();
        } catch(\Exception $e){
            DB::rollback();

			return [
				'status' 	=> 300, // GAGAL
				'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
			];

		}


        return [
            'status' => 201, // SUCCESS
            'message' => 'Data berhasil disimpan',
            'link' => '/admin/pelanggan',
        ];
    }

    public function delete($id){
        $delete = User::find($id);
        $alamat = AlamatPengiriman::where('user_id', $id)->first();

        if($delete->type !== 'pelanggan'){
            return [
                'status' => 300,
                'message' => 'Data Bukan Termasuk Pelanggan'
            ];
        }

        if($delete <> ""){
            try {
                DB::beginTransaction();

                $delete->delete();
                $alamat->delete();
                SalesPelanggan::where('pelanggan_id', $delete->pelanggan_id)->delete();

                DB::commit();
            } catch(\Exception $e){
                DB::rollback();

                return [
                    'status' 	=> 300, // GAGAL
                    'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
                ];

            }

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
