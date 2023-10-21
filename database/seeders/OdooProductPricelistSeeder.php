<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\OdooProductOriginal;
use App\Models\OdooProductPricelist;
use App\Models\SalesPelanggan;
use App\Models\User;
use DB;
use Hash;

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

        $array_id = [];
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $odoo = OdooProductPricelist::updateOrCreate([
                    "product_tmpl_id" => $data['1'],
                    "product_uom_id" => $data['4'],
                    "label" => $data['5'],
                    "type" => $data['0'],
                ],[
                    "product_tmpl_id" => $data['1'],
                    "product_uom_id" => $data['4'],
                    "price" => $data['3'],
                    "min_quantity" => $data['2'],
                    "label" => $data['5'],
                    "stock" => $data['6'],
                    "type" => $data['0'],
                ]);

                $array_id[] = $odoo->id;
            }
            $firstline = false;

        }

        fclose($csvFile);

        //delete when not in id
        OdooProductPricelist::whereNotIn('id', $array_id)->delete();

    }
}
