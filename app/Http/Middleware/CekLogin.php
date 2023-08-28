<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class CekLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {

        // dd($request->route()->getPrefix());
        if (!Auth::check() || auth()->user()->type != 'superadmin') {
            return redirect()->route('login'); //jangan lupa berikan name pada route loginnya
        }

        if(auth()->user()->access_menu == 0){
            return $next($request);
        } else {
            $mapping = [
                1 => 'admin/sales',
                2 => 'admin/pelanggan',
                3 => 'admin/produk',
                4 => 'admin/user',
                5 => 'admin/edit-absensi-sales',
                6 => 'admin/edit-kunjungan-sales',
                7 => 'admin/absensi-sales',
                8 => 'admin/kunjungan-sales',
                9 => 'admin/sales-log',
                10 => 'admin/transaksi',
                11 => 'admin/jasa-pengiriman',
                12 => 'admin/config',
            ];

            $allowedPrefix = array_map(function ($number) use ($mapping) {
                return $mapping[$number];
            }, auth()->user()->access_menu);

            // check if prefix on $mapping variable so it still can access dashboard etc.
            if(in_array($request->route()->getPrefix(), $mapping)){
                // check if prefix on allowed prefix
                if (in_array($request->route()->getPrefix(), $allowedPrefix)) {
                    return $next($request);
                } else {
                    throw new UnauthorizedHttpException('Unauthorized');
                }
            } else {
                return $next($request);
            }
        }
    }
}
