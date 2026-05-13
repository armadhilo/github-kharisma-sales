<?php

namespace App\Jobs;


use App\Models\OdooPricelist;
use App\Models\OdooProductOriginal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Helpers\OdooApi;
use \Carbon\Carbon;
use Str;
use DB;
use Hash;
use Log;

class SyncPelangganJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $data;
    protected $uid; // kept for backward compat, not used internally anymore

    public $timeout = 120;
    public $tries = 3;

    public function __construct($data, $uid = null)
    {
        $this->data = $data;
        $this->uid  = $uid; // tidak dipakai, Odoo calls via OdooApi
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try{
            $pelanggan = OdooApi::executeKw('res.partner', 'search_read',
                [[['id', '=', $this->data], ['active', '=', true]]],
                ['fields' => ['property_product_pricelist','id','email','name','mobile','display_name','npwp','npwp_name','id_nik','street','street2','city','zip']]);

            if(is_array($pelanggan) || count($pelanggan)){
                foreach($pelanggan as $item){

                    //jika pelannggan punya nama
                    if(!empty($item['name'])){
                        $user = User::updateOrCreate([
                            'pelanggan_id' => $item['id'],
                        ],[
                            'type' => 'pelanggan',
                            'type_pelanggan' => $item['property_product_pricelist'][1],
                            'pelanggan_id' => $item['id'],
                            'email' => $item['email'] == false ? null : $item['email'],
                            'nama' => trim($item['name']),
                            'telepon' => $item['mobile'],
                            'nama_toko' => trim($item['display_name']),
                            'no_npwp' => $item['npwp'],
                            'nama_npwp' => $item['npwp_name'],
                            'no_ktp' => $item['id_nik'],
                            'street' => $item['street'],
                            'street2' => $item['street2'],
                            'city' => $item['city'],
                            'zip' => $item['zip'],
                            'alamat_toko' => $item['street'] . ($item['street2'] != false ? ' - ' . $item['street2'] : '') . ($item['city'] != false ? ' - ' . $item['city'] : '') . ($item['zip'] != false ? ' - ' . $item['zip'] : ''),
                        ]);

                        if($user->wasRecentlyCreated){
                            User::where('id', $user->id)->update([
                                'password' => Hash::make('pelanggan')
                            ]);
                        }
                    }
                }
            }
        }

        catch(\Exception $e){

            Log::error($e);

            $this->fail(new \Exception($e));
        }
    }
}
