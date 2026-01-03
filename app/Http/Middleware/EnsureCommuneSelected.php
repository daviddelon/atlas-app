<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommuneSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('current_commune_code')) {
            $availableCodes = config('app.available_commune_codes');
            session(['current_commune_code' => $availableCodes[0] ?? '34343']);
        }

        return $next($request);
    }
}
