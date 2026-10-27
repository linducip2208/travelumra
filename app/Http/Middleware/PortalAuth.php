<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('portal_pilgrim_id')) {
            return redirect()->route('portal.login');
        }

        // Validasi pilgrim masih aktif
        $pilgrim = \App\Models\Pilgrim::find($request->session()->get('portal_pilgrim_id'));
        if (! $pilgrim || ! $pilgrim->is_active) {
            $request->session()->invalidate();
            return redirect()->route('portal.login');
        }

        return $next($request);
    }
}
