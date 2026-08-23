<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthKurirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('isLogin') || session('role') !== 'Kurir') {
            return redirect('/login')->with('error', 'Silakan login sebagai Kurir terlebih dahulu!');
        }
        return $next($request);
    }
}