<?php

namespace App\Helpers;

ini_set('max_execution_time', '0');
require_once(base_path() . '/public/ripcord-master/ripcord.php');

use ripcord;

class OdooApi
{
    protected static $db;
    protected static $username;
    protected static $password;
    protected static $uid;
    protected static $models;

    public static function createCustomer($request){

        $url = 'http://103.215.177.203:8096';
        $db = 'odoo_kharisma_stationery_test5';
        $username = "kevin_kevin";
        $password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");

        $partnerData = array(
            [
                'name' => $request->name,
                'street' => $request->street,
                'country_id' => 100,
                'phone' => $request->phone,
                'zip' => $request->zip
            ]
        );

        $customer = $models->execute_kw($db, $uid, $password,
        'res.partner', 'create', [$partnerData]);

        return $customer;
    }

    public static function updateCustomer($request){

        $url = 'http://103.215.177.203:8096';
        $db = 'odoo_kharisma_stationery_test5';
        $username = "kevin_kevin";
        $password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");

        $partnerData = array(
                'street' => $request->street,
                'country_id' => 100,
                'phone' => $request->phone,
                'zip' => $request->zip
        );

        $customer = $models->execute_kw($db, $uid, $password,
        'res.partner', 'write', array(array($request->pelanggan_id), $partnerData));

        return $customer;
    }
}