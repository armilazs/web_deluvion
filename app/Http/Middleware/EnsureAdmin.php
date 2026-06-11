<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('firebase_uid')) {
            return redirect()->route('login');
        }

        if ($request->session()->get('role') !== 'admin') {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
