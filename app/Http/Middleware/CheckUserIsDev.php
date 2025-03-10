<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facades\Debugbar;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsDev
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->isDev()) {
            Debugbar::enable();
        }
        else {
            Debugbar::disable();
        }
        return $next($request);
    }
}
