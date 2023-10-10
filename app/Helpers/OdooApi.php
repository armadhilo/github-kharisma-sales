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

        $url = config('services.odoo.odoo_url');
        $db = config('services.odoo.odoo_db');
        $username = config('services.odoo.odoo_username');
        $password = config('services.odoo.odoo_password');

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");

        $partnerData = array(
            [
                'name' => $request->name,
                'country_id' => 100,
                'display_name' => $request->nama_toko,
                'phone' => $request->phone,
                'npwp' => $request->no_npwp,
                'npwp_name' => $request->nama_npwp,
                'id_nik' => $request->no_ktp,
                'email' => $request->email,
                'name' => $request->nama,
                'mobile' => $request->telepon,
                'zip' => $request->zip,
            ]
        );

        $customer = $models->execute_kw($db, $uid, $password,
        'res.partner', 'create', [$partnerData]);

        return $customer;
    }

    public static function updateCustomer($parameter){

        $url = config('services.odoo.odoo_url');
        $db = config('services.odoo.odoo_db');
        $username = config('services.odoo.odoo_username');
        $password = config('services.odoo.odoo_password');

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");

        $partnerData = array(
                'street' => $parameter['street'],
                'country_id' => 100,
                'phone' => $parameter['phone'],
                'zip' => $parameter['zip'],
                'email' => $parameter['email'],
                'mobile' => $parameter['mobile'],
                'npwp' => $parameter['npwp'],
                'npwp_name' => $parameter['npwp_name'],
                'id_nik' => $parameter['id_nik'],
                'street' => $parameter['street'],
                'street2' => $parameter['street2'],
                'city' => $parameter['city'],
        );

        $customer = $models->execute_kw($db, $uid, $password,
        'res.partner', 'write', array(array($parameter['pelanggan_id']), $partnerData));

        return $customer;
    }

    public static function updateEmailSales($parameter){

        $url = config('services.odoo.odoo_url');
        $db = config('services.odoo.odoo_db');
        $username = config('services.odoo.odoo_username');
        $password = config('services.odoo.odoo_password');

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");

        $partnerData = array(
                'name' => $parameter['name'],
                'email' => $parameter['email'],
                'mobile' => $parameter['mobile'],
        );

        $customer = $models->execute_kw($db, $uid, $password,
        'res.partner', 'write', array(array($parameter['pelanggan_id']), $partnerData));

        return $customer;
    }
}
