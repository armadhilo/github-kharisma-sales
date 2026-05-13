<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\SalesOrder;
use App\Helpers\OdooApi;
use DB;

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
        // Inisialisasi Odoo dilakukan di handle() agar tidak berjalan
        // saat Artisan melakukan bootstrap & registrasi command.
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $sales_order = DB::table('sales_order')->whereIn('status', ['draft','sale'])->get()->pluck('odoo_order_id');
            $stringCollection = collect($sales_order)->map(function ($item) {
                return (string) $item;
            });

            $stringArray = $stringCollection->all();

            \Log::info($stringArray);

            $odoo_api = OdooApi::executeKw('sale.order', 'search_read',
                [[['id', '=', $stringArray]]], ['fields' => ['state']]);

            \Log::info($odoo_api);

            foreach($odoo_api as $item){
                DB::table('sales_order')->where('odoo_order_id', (string)$item['id'])->update(['status' => (string)$item['state']]);
            }

            \Log::info("Cron is working fine [sync-status-odoo]->" . Carbon::now()->format('Y-m-d H:i:s'));
        }
        catch (\Exception $e) {
            \Log::alert($e);
        }
    }
}
