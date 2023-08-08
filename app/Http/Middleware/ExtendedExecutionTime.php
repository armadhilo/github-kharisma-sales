<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExtendedExecutionTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        set_time_limit(0); // or any other value you prefer
        return $next($request);
    }
}
