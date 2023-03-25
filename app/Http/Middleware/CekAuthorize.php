<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Models\MasterMenu;
use Illuminate\Http\Request;

use Closure;

class CekAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $url_menu = $request->segment(1);
        $url_submenu = $request->segment(2);

        $master_menu = MasterMenu::whereIn('id', session()->get('json_menu'))->where(['menu' => $url_menu])->orderBy('index','asc');
        if($url_submenu != "" && $url_submenu != 'report' && $url_submenu != 'datatables' && $url_submenu != 'detail' && $url_submenu != 'delete'){
            $master_menu = $master_menu->where('submenu', $url_submenu);
        }
        if($master_menu->count() > 0){
            return $next($request);
        }else{
            abort (401); //jangan lupa berikan name pada route loginnya
        }
    }
}
