<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\SalesPelanggan;
use App\Models\User;
use DB;
use Hash;

class SalesCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SalesPelanggan::truncate();

        // $csvFile = fopen(base_path("database/data/sales_customer.csv"), "r");

        // $firstline = true;
        // while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
        //     if (!$firstline) {
        //         SalesPelanggan::create([
        //             "sales_id" => $data['2'],
        //             "pelanggan_id" => $data['0']
        //         ]);
        //     }
        //     $firstline = false;
        // }

        // fclose($csvFile);

        //update customer where sales
        $data = DB::select("
            SELECT pelanggan_id FROM users WHERE pelanggan_id IN (SELECT DISTINCT sales_id FROM sales_pelanggan)
        ");

        foreach($data as $item){
            $password = Hash::make('sales123');
            User::where('pelanggan_id', $item->pelanggan_id)->update([
                'type' => '1',
                'password' => $password
            ]);
        }

    }
}
