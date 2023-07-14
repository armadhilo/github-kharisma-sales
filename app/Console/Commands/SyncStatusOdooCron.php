<?php

namespace App\Console\Commands;

ini_set('max_execution_time', '0');
require_once(base_path() . '/public/ripcord-master/ripcord.php');

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\SalesOrder;
use DB;
use ripcord;

class SyncStatusOdooCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-status-odoo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk sync status dengan database odoo';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $models;

    public function __construct()
    {
        parent::__construct();
        $url = 'http://103.215.177.203:8096';
        $this->db = 'odoo_kharisma_stationery_test5';
        $this->username = "kevin_kevin";
        $this->password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        $this->models = ripcord::client("$url/xmlrpc/2/object");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // app()->call('App\Http\Controllers\OdooController@sync_status');

        try{
            $sales_order = DB::table('sales_order')->whereIn('status', ['draft','sale'])->get()->pluck('odoo_order_id');
            $stringCollection = collect($sales_order)->map(function ($item) {
                return (string) $item;
            });


            $stringArray = $stringCollection->all();

            \Log::info($stringArray);

            $odoo_api = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'sale.order', 'search_read', [[['id','=', $stringArray]]], ['fields' => ['state']]);

            \Log::info($odoo_api);

            foreach($odoo_api as $item){
                DB::table('sales_order')->where('odoo_order_id', (string)$item['id'])->update(['status' => (string)$item['state']]);
            }

            \Log::info("Cron is working fine [sales-kunjungan-absence]->" . Carbon::now()->format('Y-m-d H:i:s'));

        }
        catch (\Exception $e) {
            \Log::alert($e);
        }
    }
}
