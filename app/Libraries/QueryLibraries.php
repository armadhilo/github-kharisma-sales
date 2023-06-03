<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

use DB;
use Carbon\Carbon;
use App\Helpers\Convert;

use App\Models\MasterSubkategori;
use App\Models\Transaksi;
use App\Models\PoinLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\MasterProduk;
use App\Models\MasterProdukLog;

use App\Mail\TransaksiCancel;

class QueryLibraries{

    public static function data_urutan_subkategori_by_id($subkategori_id){
        $array = [];
        $subkategori_first = MasterSubkategori::find($subkategori_id);
        $subkategori = MasterSubkategori::where('level', '<', $subkategori_first->level)->where('master_kategori_id', $subkategori_first->master_kategori_id)->get();

        $temp_parent = $subkategori_id;
        for($i = 1; $i <= $subkategori_first->level; $i++){
            $master_subkategori = MasterSubkategori::find($temp_parent);
            $temp_parent = $master_subkategori->parent_id;

            $array[] = [
                'id' => $master_subkategori->id,
                'subkategori' => $master_subkategori->subkategori,
            ];
        }

        return array_reverse($array);
    }

    public static function transaksi_cancel(){
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::with('user', 'transaksi_produk')->where('datetime_batas_pembayaran', '<=', Carbon::now())->where('status', 0)->get();
            if(count($transaksi)){
                $array = [];
                foreach($transaksi as $item){
                    //MENGEMBALIKAN POIN
                    $poin_log = PoinLog::where(['transaksi_id' => $item->id, 'status' => '-'])->first();
                    if(!empty($poin_log)){
                        PoinLog::create([
                            'user_id' => $item->user->id,
                            'transaksi_id' => $item->id,
                            'status' => '+',
                            'nominal' => $poin_log->nominal,
                            'sisa_poin' => $item->user->poin + $poin_log->nominal,
                            'keterangan' => 'pengembalian poin',
                        ]);
                        User::where('id', $item->user->id)->update(['poin' => $item->user->poin + $poin_log->nominal]);
                    }

                    Notification::create([
                        'user_id' => $item->user->id,
                        'transaksi_id' => $item->id,
                        'notification_type' => 'transaksi_cancel',
                        'is_new' => '1',
                        'status' => '1',
                    ]);

                    //MENGEMBALIKAN STOCK PRODUCT
                    foreach($item->transaksi_produk as $item2){
                        $master_produk = MasterProduk::where('id', $item2->master_produk_id)->withTrashed()->first();
                        if(!empty($master_produk)){
                            $stock_lama = $master_produk->stock;
                            MasterProduk::where('id', $master_produk->id)->update([
                                'stock' => $item2->quantity + $stock_lama,
                            ]);
                        }

                        //MASTER PRODUK LOG
                        $tipe = '+';
                        $keterangan = 'Penambahan stock dari transaksi ' . $item->no_invoice . ' yang di cancel atau tolak';
                        $quantity = $item2->quantity;

                        MasterProdukLog::create([
                            'master_produk_id' => $master_produk->id,
                            'tipe' => $tipe,
                            'quantity' => $quantity,
                            'harga' => $item2->harga,
                            'keterangan' => $keterangan,
                        ]);

                    }

                    $data = [
                        'nama' => $item->user->nama,
                        'no_invoice' => $item->no_invoice,
                        'total_harga' => $item->total_harga,
                    ];
                    $email = $item->user->email;
                    Mail::to($email)->send(new TransaksiCancel($data));

                    if(!empty($item->user->no_telepon)){
                        $array[] = [
                            'receiver' =>  Convert::convert_telepon_to_whatsapp($item->user->no_telepon),
                            'message' =>  ["Maaf transaksimu $item->no_invoice kami batalin nih karena melewati masa pembayaran atau pembayaranmu kami tolak. Salam Hangat KharismaOnline"],
                        ];
                    }
                }
            }

            Http::post(env('URL_WA_SERVER').'/chats/send-bulk?id=notifications', $array);

            Transaksi::where('datetime_batas_pembayaran', '<=', Carbon::now())->where('status', 0)->update(['status' => '5']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

}
