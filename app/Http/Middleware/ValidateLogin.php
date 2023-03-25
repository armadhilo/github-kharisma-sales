<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiFormatter;
use Closure;

class ValidateLogin
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
        if (!Auth::guard('api')->check()) {
            return ApiFormatter::error(401, 'Unauthorized');
        }

        return $next($request);
    }
}
