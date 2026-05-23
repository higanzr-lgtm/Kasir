<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('isLogin')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }
        if (session('role') !== 'Customer') {
            return redirect()->route('beranda')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
        return $next($request);
    }
}