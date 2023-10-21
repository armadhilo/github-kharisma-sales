<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\OdooProductOriginal;
use App\Models\OdooProductPricelist;
use App\Models\OdooProductUom;
use App\Models\SalesPelanggan;
use App\Models\User;
use DB;
use Hash;
use Carbon\Carbon;

class OdooProductPricelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/data_pricelist_for_kevin.csv"), "r");

        OdooProductUom::truncate();
        OdooProductPricelist::truncate();

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {

                OdooProductUom::insert([
                    "product_tmpl_id" => $data['1'],
                    "product_uom_id" => $data['4'],
                    "label" => $data['5'],
                    "stock" => $data['6'],
                    "type" => $data['0'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                OdooProductPricelist::insert([
                    "product_tmpl_id" => $data['1'],
                    "product_uom_id" => $data['4'],
                    "price" => $data['3'],
                    "min_quantity" => $data['2'],
                    "label" => $data['5'],
                    "stock" => $data['6'],
                    "type" => $data['0'],
                ]);
            }
            $firstline = false;

        }

        fclose($csvFile);
    }

    // $get = OdooProductPricelist::all();

    // OdooProductUom::truncate();
    // foreach($get as $item){
    //     OdooProductUom::updateOrInsert([
    //         'product_tmpl_id' => $item->product_tmpl_id,
    //         'product_uom_id' => $item->product_uom_id,
    //         'label' => $item->label,
    //         'type' => $item -> type,
    //     ],[
    //         'product_tmpl_id' => $item->product_tmpl_id,
    //         'product_uom_id' => $item->product_uom_id,
    //         'label' => $item->label,
    //         'stock' => $item->stock,
    //         'type' => $item -> type,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now(),
    //     ]);
    // }
}
