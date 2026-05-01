<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthEmployee
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('employee')) {
            return redirect('/karyawan')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
