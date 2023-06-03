<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\WishlistRequest;
use App\Models\OdooProduct;
use App\Models\Wishlist;

use App\Http\Requests\PelangganRequest;

class OrderController extends Controller
{
    function check_voucher(request $request){
        return $this->private_check_voucher($request);
    }

    function transaksi(request $request){

        $validator = Validator::make($request->all(), [
            'master_produk_id.*' => 'required',
            'cart_quantity.*' => 'required',
            'cart_harga.*' => 'required',
            'alamat_pengiriman_id' => 'required',
            'harga_pengiriman' => 'required',
            'kode_voucher_status' => 'required',
            'jasa_pengiriman' => 'required',
        ]);


        if($validator->fails()) {
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        //ERROR BACKPAGE
        $cart = Cart::whereIn('master_produk_id',$request->master_produk_id)->where('user_id', auth()->user()->id)->count();
        if($cart == 0){
            return [
                'status' => 301,
                'message' => 'Anda sudah melakukan checkout untuk keranjang ini',
                'link' => '/errors/checkout-gagal'
            ];
        }

        $master_produk_id = $request->master_produk_id;
        $cart_quantity = $request->master_produk_id;
        $cart_harga = $request->master_produk_id;

        //SUM ULANG SUB TOTAL dan CHECK STOCK
        $sub_total = 0;
        $master_produk = MasterProduk::all();
        foreach($request->master_produk_id as $key => $item){

            if($master_produk->firstWhere('id', $item)->stock < $request->cart_quantity[$key]){
                return [
                    'status' => 301,
                    'link' => '/cart',
                    'message' => 'Terdapat barang yang tidak dapat kami proses karena stock sudah habis'
                ];
            }

            $sub_total += $master_produk->firstWhere('id', $item)->harga_jual * $request->cart_quantity[$key];

        }

        //INISIALISASI ONLY HARGA PRODUK
        $request->request->add(['total_harga_only_produk' => $sub_total]);

        //APABILA ADA VOUCHER CHECK ULANG
        if($request->kode_voucher_status == 'true'){
            $request->request->add(['voucher' => $request->kode_voucher]);
            $request->request->add(['total_harga' => $sub_total]);

            $private_check_voucher = $this->private_check_voucher($request);
            if($private_check_voucher['status'] == 200){
                if($private_check_voucher['jenis_voucher'] == 'persen'){
                    $request->request->add(['point' => $private_check_voucher['data']]);
                }else{
                    $explode_kode_voucher = Explode('|', $private_check_voucher['data']);
                    // $master_produk_id[] = $explode_kode_voucher[0];
                    // $cart_quantity[] = $explode_kode_voucher[1];
                    // $cart_harga[] = 0;

                    //INSISIALISASI TAMBAHAN
                    $master_produk_voucher = $explode_kode_voucher[0];
                    $cart_quantity_voucher = $explode_kode_voucher[1];
                }
            }
        }

        //APABILA ADA POIN CHECK ULANG
        if($request->poin_active == 'true'){
            $private_check_poin = $this->private_check_poin($request);
            if($private_check_poin['status'] == 200){
                $sub_total -= $private_check_poin['data'];
            }
        }

        //CEK POTONGAN HARGA GROSIR
        $private_cek_potongan_harga_grosir = $this->private_cek_potongan_harga_grosir($request);
        if($private_cek_potongan_harga_grosir['status'] == 200){
            $sub_total -= $private_cek_potongan_harga_grosir['data'];
        }

        //GENERATE NO_INVOICE
        $no_invoice = $this->generate_no_invoice();
        $request->request->add(['no_invoice' => $no_invoice]);

        //TAMBAH ONGKOS KIRIM
        $sub_total += $request->harga_pengiriman;

        $request->request->add(['user_id' => auth()->user()->id]);
        $request->request->add(['datetime_batas_pembayaran' => Carbon::now()->addHours(12)]);
        $request->request->add(['status' => 0]);
        $request->request->add(['uuid' =>  Str::uuid()]);

        //tambah dengan 3 angka random untuk validasi pembayaran (HARGA UNIQUE)
        $harga_unique = rand(1,200);
        $sub_total += $harga_unique;

        $request->request->add(['harga_unique' => $harga_unique]);
        $request->request->add(['total_harga' => $sub_total]);

        DB::beginTransaction();

		try{

            //CEK KALAU ADA POTONGAN HARGA GROSIR
            $request->request->add(['potongan_harga_grosir' => $private_cek_potongan_harga_grosir['data'] ?? 0]);

            //ADD POINT USE
            if($request->poin_active == 'true'){
                if($private_check_poin['status'] == 200){
                    $request->request->add(['point_use' => $private_check_poin['data']]);
                }
            }

            //ADD POINT BONUS
            $master_global = MasterGlobal::where('tipe', 'bonus_poin_pembelian')->first();
            $harga_barang = $request->total_harga_only_produk - $request->point_use;
            $request->request->add(['point_bonus' => floor($harga_barang/$master_global->value1) * $master_global->value2]);

			$transaksi = Transaksi::create($request->all());

            $poin = auth()->user()->poin;

            if($request->poin_active == 'true'){
                if($private_check_poin['status'] == 200){

                    $poin -= $private_check_poin['data'];

                    //POIN
                    PoinLog::create([
                        'user_id' => auth()->user()->id,
                        'transaksi_id' => $transaksi->id,
                        'status' => '-',
                        'nominal' => $private_check_poin['data'],
                        'sisa_poin' => $poin,
                    ]);
                    User::where('id', auth()->user()->id)->update(['poin' => $poin]);
                }
            }

            $user = User::find(auth()->user()->id);
            $user->poin = $poin;
            $user->save();

            foreach($request->master_produk_id as $key => $item){
                transaksiProduk::create([
                    'transaksi_id' => $transaksi->id,
                    'master_produk_id' => $item,
                    'quantity' => $request->cart_quantity[$key],
                    'harga' => $request->cart_harga[$key],
                ]);
            }

            //CREATE TRANSAKSI_PRODUK VOUCHER BARANG
            if(isset($cart_quantity_voucher) && isset($master_produk_voucher)){
                transaksiProduk::create([
                    'transaksi_id' => $transaksi->id,
                    'master_produk_id' => $master_produk_voucher,
                    'quantity' => $cart_quantity_voucher,
                    'harga' => 0,
                    'kode_voucher' => $request->kode_voucher,
                ]);
            }

            //NOTIFICATION
            Notification::create([
                'user_id' => $request->user_id,
                'transaksi_id' => $transaksi->id,
                'notification_type' => 'menunggu_pembayaran',
            ]);

            //KURANGI STOCK PRODUCT
            foreach($request->master_produk_id as $key => $item){

                $stock_baru = Cart::where(['user_id' => auth()->user()->id, 'master_produk_id' => $item])->first()->quantity;

                $master_produk = MasterProduk::find($item);
                $stock_lama = $master_produk->stock;
                $master_produk->stock = $stock_lama - $stock_baru;
                $master_produk->save();

                //MASTER PRODUK LOG
                $tipe = '-';
                $keterangan = 'Pengurangan stock dari transaksi ' . $transaksi->no_invoice;
                $quantity = $stock_baru;

                MasterProdukLog::create([
                    'master_produk_id' => $item,
                    'tipe' => $tipe,
                    'quantity' => $quantity,
                    'harga' => $request->cart_harga[$key],
                    'keterangan' => $keterangan,
                ]);
            }

            //KURANGI STOCK PRODUCT VOUCHER BARANG
            if(isset($cart_quantity_voucher) && isset($master_produk_voucher)){
                $master_produk = MasterProduk::find($master_produk_voucher);
                $master_produk->stock = $master_produk->stock - $cart_quantity_voucher;
                $master_produk->save();

                //MASTER PRODUK LOG
                $tipe = '-';
                $keterangan = 'Pengurangan stock dari voucher '.$request->kode_voucher.' transaksi ' . $transaksi->no_invoice;
                $quantity = $cart_quantity_voucher;

                MasterProdukLog::create([
                    'master_produk_id' => $master_produk_voucher,
                    'tipe' => $tipe,
                    'quantity' => $quantity,
                    'harga' => 0,
                    'keterangan' => $keterangan,
                ]);
            }

            //DELETE PRODUK FROM CART
            Cart::where('user_id', auth()->user()->id)->whereIn('master_produk_id', $request->master_produk_id)->delete();

			DB::commit();

            $data = [
                'nama' => $user->nama,
                'batas_pembayaran' => $request->datetime_batas_pembayaran,
                'no_invoice' => $request->no_invoice,
                'total_harga' => $request->total_harga,
            ];
            $email = $user->email;
            Mail::to($email)->send(new TransaksiBelumBayar($data));

            $link = '/pembayaran/' . $request->uuid;

			return [
				'status' => 201, // SUCCESS
				'link' => $link,
				'message' => 'Yeay! anda akan mendapat '. number_format($transaksi->point_bonus,0,',','.') .' poin dari belanja ini. Tunggu sebentar ya kami sedang generate pembayaranmu'
			];
		}

		catch(\Exception $e){

			DB::rollback();

			return [
				'status' 	=> 300, // GAGAL
				'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
			];

		}
    }

    public function pembayaran($uuid){
        DB::beginTransaction();

		try{

            $transaksi = Transaksi::where('uuid', $uuid)->with('user')->first();
            $transaksi->status = Transaksi::KONFIRMASI_ADMIN;
            $transaksi->save();

            Notification::create([
                'user_id' => $transaksi->user_id,
                'transaksi_id' => $transaksi->id,
                'notification_type' => 'sudah_bayar',
            ]);

            $data = [
                'nama' => $transaksi->user->nama,
                'no_invoice' => $transaksi->no_invoice,
                'total_harga' => $transaksi->total_harga,
            ];

            $email = $transaksi->user->email;
            Mail::to($email)->send(new TransaksiSudahBayar($data));

            //KIRIM PERSEORANGAN
            Http::post(env('URL_WA_SERVER').'/chats/send?id=notifications', [
                'receiver' =>  Convert::convert_telepon_to_whatsapp($transaksi->user->no_telepon),
                'message'  => [ 'text' => "Pembayaranmu untuk transaksi $transaksi->no_invoice sedang kami review. Apabila proses sudah selesai akan kami kabari ya :) Salam Hangat KharismaOnline" ]
            ]);

            DB::commit();

            return [
                'status' => 201,
                'link' => '/errors/transaksi-berhasil',
                'message' => 'Pembayaran berhasil, selanjutnya akan kami proses dulu pembayaranmu'
            ];
		}

		catch(\Exception $e){

			DB::rollback();

			return [
				'status' 	=> 300, // GAGAL
				'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
			];

		}
    }

    private function private_check_voucher($request){
        $voucher = $request->voucher;
        $user_id = auth()->user()->id;

        $data_voucher = KodeVoucher::with('master_produk_id_beli', 'master_produk_id_bonus')->active()->where('kode_voucher', $voucher)->first();
        if(empty($data_voucher)){
            $data_voucher = KodeVoucher::with('master_produk_id_beli', 'master_produk_id_bonus')->mustClaim()->where('kode_voucher', $voucher)->whereHas('kode_voucher_user', function($q) use ($user_id){
                $q->active();
            })->first();
        }else{
            if($data_voucher->maksimal_penggunaan <= $data_voucher->total_penggunaan){
                return [
                    'status' => 300,
                    'message' => 'Kode Voucher sudah limit penggunaan',
                ];
            }
        }

        if(empty($data_voucher)){
            return [
                'status' => 300,
                'message' => 'Kode voucher tidak ditemukan',
            ];
        }

        if($data_voucher->tipe == 'barang'){

            $cart = Cart::checkoutActive()->where('master_produk_id', $data_voucher->master_produk_id_beli)->where('quantity', '>=', $data_voucher->minimal_produk_beli)->first();
            if(empty($cart)){
                return [
                    'status' => 300,
                    'message' => 'Anda tidak memenuhi syarat untuk mendapatkan voucher ini'
                ];
            }

            $master_barang_bonus = MasterProduk::where('id', $data_voucher->master_produk_id_bonus)->first();
            if($master_barang_bonus->stock < $data_voucher->jumlah_produk_bonus){
                return [
                    'status' => 300,
                    'message' => 'Oops! Barang bonus sudah habis, voucher tidak berlaku'
                ];
            }

            return [
                'status' => 200,
                'jenis_voucher' => 'barang',
                'data' => $data_voucher->master_produk_id_bonus ."|". $data_voucher->jumlah_produk_bonus,
                'message' => $data_voucher->deskripsi_bonus,
            ];

        }else{
            //cek untuk potongan apakah memenuhi minimal pembelian
            $total_harga = $request->total_harga;

            if($data_voucher->persen_minimal_pembelian > $total_harga){
                return [
                    'status' => 300,
                    'message' => 'Minimal pembelian voucher ini adalah ' . "Rp " . number_format($data_voucher->persen_minimal_pembelian,0,',','.')
                ];
            }

            $potongan_harga = ($data_voucher->persen_presentase_potongan / 100) * $total_harga;
            if($potongan_harga > $data_voucher->persen_maksimal_potongan){
                $potongan_harga = $data_voucher->persen_maksimal_potongan;
            }

            return [
                'status' => 200,
                'jenis_voucher' => 'persen',
                'data' => $potongan_harga,
                'message' => 'Cashback ' . "Rp " . number_format($potongan_harga,0,',','.'),
            ];

        }
    }

    private function generate_no_invoice(){
        $tahun = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $date = Carbon::now()->format('d');
        $count_transaksi = Transaksi::whereYear('created_at', $tahun)->count();

        return 'INV-' . $tahun .'/'. $date . $month . str_pad($count_transaksi + 1, 6, "0", STR_PAD_LEFT);
    }
}
