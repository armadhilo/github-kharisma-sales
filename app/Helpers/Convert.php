<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Convert {

    public static function convert_to_number($rupiah){
        return intval(preg_replace('/,.*|[^0-9]/', '', $rupiah));
    }

    public static function convert_to_double($data){
        $data = str_replace(".","", $data);
        return str_replace(",",".", $data);
    }

    public static function convert_telepon_to_whatsapp($no_telepon){
        if(substr(trim($no_telepon), 0, 1)=='0'){
            return '62'.substr(trim( $no_telepon), 1);
        }

        return $no_telepon;
    }
}
?>

