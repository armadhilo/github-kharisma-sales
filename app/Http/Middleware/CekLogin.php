<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Models\MasterMenu;
use Illuminate\Http\Request;

use Closure;

class CekLogin
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
        if (!Auth::check()) {
            return redirect()->route('login'); //jangan lupa berikan name pada route loginnya
        }
        return $next($request);
    }
}
