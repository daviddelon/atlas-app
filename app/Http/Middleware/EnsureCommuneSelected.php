<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            // Sélectionner la première commune qui a des données
            $selectedCode = null;
            foreach ($availableCodes as $code) {
                $hasData = DB::table('observations')->where('code', $code)->exists();
                if ($hasData) {
                    $selectedCode = $code;
                    break;
                }
            }

            // Si aucune commune n'a de données, prendre la première disponible
            if (!$selectedCode) {
                $selectedCode = $availableCodes[0] ?? '34343';
            }

            session(['current_commune_code' => $selectedCode]);
        }

        return $next($request);
    }
}
