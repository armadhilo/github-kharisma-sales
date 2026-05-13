<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OdooApi
{
    /**
     * Durasi cache UID Odoo dalam menit.
     * Sesuaikan dengan session timeout Odoo di server.
     */
    const UID_CACHE_TTL = 60; // menit

    // -------------------------------------------------------------------------
    // Core JSON-RPC
    // -------------------------------------------------------------------------

    /**
     * Kirim request JSON-RPC ke Odoo.
     * Mengembalikan `result` dari response, atau array fault jika error.
     *
     * @param  string  $service  'common' | 'object'
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function callJsonRpc(string $service, string $method, array $args)
    {
        $url = config('services.odoo.odoo_url');

        if (empty($url)) {
            throw new \RuntimeException(
                '[OdooApi] ODOO_URL belum dikonfigurasi di .env. ' .
                'Tambahkan: ODOO_URL=http://your-odoo-server:8069'
            );
        }

        // Pastikan URL tidak punya trailing slash ganda
        $url = rtrim($url, '/');

        $response = Http::timeout(30)->post("$url/jsonrpc", [
            'jsonrpc' => '2.0',
            'method'  => 'call',
            'id'      => 1,
            'params'  => [
                'service' => $service,
                'method'  => $method,
                'args'    => $args,
            ],
        ]);

        $data = $response->json();

        // Normalisasi error ke format fault agar sisa kode konsisten
        if (isset($data['error'])) {
            $err = $data['error'];
            return [
                'faultCode'   => $err['code'] ?? -1,
                'faultString' => $err['data']['message'] ?? $err['message'] ?? 'Unknown Odoo error',
            ];
        }

        return $data['result'] ?? null;
    }

    // -------------------------------------------------------------------------
    // Auth & Cache
    // -------------------------------------------------------------------------

    /**
     * Mengembalikan cache key untuk UID Odoo.
     */
    protected static function uidCacheKey(): string
    {
        $url      = config('services.odoo.odoo_url');
        $db       = config('services.odoo.odoo_db');
        $username = config('services.odoo.odoo_username');

        return 'odoo_uid_' . md5($url . $db . $username);
    }

    /**
     * Mengembalikan UID hasil autentikasi Odoo via JSON-RPC.
     * UID di-cache selama UID_CACHE_TTL menit.
     *
     * @param  bool  $forceRefresh  Paksa login ulang & perbarui cache.
     * @return int|false
     */
    public static function getUid(bool $forceRefresh = false)
    {
        $db       = config('services.odoo.odoo_db');
        $username = config('services.odoo.odoo_username');
        $password = config('services.odoo.odoo_password');

        $cacheKey = self::uidCacheKey();

        if ($forceRefresh) {
            Cache::forget($cacheKey);
            Log::info('[OdooApi] Cache UID di-reset, melakukan re-authenticate ke Odoo.');
        }

        $uid = Cache::remember($cacheKey, self::UID_CACHE_TTL * 60, function () use ($db, $username, $password) {
            Log::info('[OdooApi] Call authenticate ke Odoo.' . date('Y-m-d H:i:s'));
            return self::callJsonRpc('common', 'authenticate', [$db, $username, $password, []]);
        });

        return $uid;
    }

    // -------------------------------------------------------------------------
    // Fault Detection
    // -------------------------------------------------------------------------

    /**
     * Mendeteksi apakah response Odoo mengindikasikan UID/token kadaluarsa.
     *
     * @param  mixed  $response
     * @return bool
     */
    public static function isExpiredUid($response): bool
    {
        if (!is_array($response) || !isset($response['faultCode'])) {
            return false;
        }

        $msg = strtolower($response['faultString'] ?? '');

        return str_contains($msg, 'access denied')
            || str_contains($msg, 'session expired')
            || str_contains($msg, 'not allowed')
            || (int) $response['faultCode'] === 3;
    }

    // -------------------------------------------------------------------------
    // Public API — execute_kw wrapper dengan retry otomatis
    // -------------------------------------------------------------------------

    /**
     * Wrapper execute_kw dengan retry otomatis ketika UID kadaluarsa.
     *
     * Alur:
     *   1. Coba dengan UID dari cache
     *   2. Jika response = Access Denied → invalidate cache, re-auth, retry sekali
     *   3. Return result (atau log error jika retry juga gagal)
     *
     * @param  string  $model
     * @param  string  $method
     * @param  array   $args
     * @param  array   $kwargs
     * @return mixed
     */
    public static function executeKw(string $model, string $method, array $args, array $kwargs = [])
    {
        $db       = config('services.odoo.odoo_db');
        $password = config('services.odoo.odoo_password');

        // --- Percobaan pertama dengan UID dari cache ---
        $uid      = self::getUid();
        $response = self::callJsonRpc('object', 'execute_kw', [$db, $uid, $password, $model, $method, $args, $kwargs]);

        if (!self::isExpiredUid($response)) {
            return $response;
        }

        // --- UID expired: invalidate cache lalu retry sekali ---
        Log::warning('[OdooApi] UID Odoo kadaluarsa. Melakukan re-authenticate dan retry...', [
            'model'       => $model,
            'method'      => $method,
            'faultCode'   => $response['faultCode'] ?? '-',
            'faultString' => $response['faultString'] ?? '-',
        ]);

        $uid      = self::getUid(true);
        $response = self::callJsonRpc('object', 'execute_kw', [$db, $uid, $password, $model, $method, $args, $kwargs]);

        if (self::isExpiredUid($response)) {
            Log::error('[OdooApi] Re-authenticate gagal. Odoo tetap mengembalikan fault.', [
                'faultCode'   => $response['faultCode'] ?? null,
                'faultString' => $response['faultString'] ?? null,
            ]);
        }

        return $response;
    }

    // -------------------------------------------------------------------------
    // Convenience Methods
    // -------------------------------------------------------------------------

    public static function createCustomer($request)
    {
        $partnerData = array(
            [
                'name'         => $request->nama,
                'country_id'   => 100,
                'display_name' => $request->nama_toko,
                'phone'        => $request->phone,
                'npwp'         => $request->no_npwp,
                'npwp_name'    => $request->nama_npwp,
                'id_nik'       => $request->no_ktp,
                'email'        => $request->email,
                'mobile'       => $request->telepon,
                'zip'          => $request->zip,
            ]
        );

        return self::executeKw('res.partner', 'create', [$partnerData]);
    }

    public static function updateCustomer($parameter)
    {
        $partnerData = array(
            'street'     => $parameter['street'],
            'country_id' => 100,
            'phone'      => $parameter['phone'],
            'zip'        => $parameter['zip'],
            'email'      => $parameter['email'],
            'mobile'     => $parameter['mobile'],
            'npwp'       => $parameter['npwp'],
            'npwp_name'  => $parameter['npwp_name'],
            'id_nik'     => $parameter['id_nik'],
            'street2'    => $parameter['street2'],
            'city'       => $parameter['city'],
        );

        return self::executeKw('res.partner', 'write', [[$parameter['pelanggan_id']], $partnerData]);
    }

    public static function updateEmailSales($parameter)
    {
        $partnerData = array(
            'name'   => $parameter['name'],
            'email'  => $parameter['email'],
            'mobile' => $parameter['mobile'],
        );

        return self::executeKw('res.partner', 'write', [[$parameter['pelanggan_id']], $partnerData]);
    }
}
