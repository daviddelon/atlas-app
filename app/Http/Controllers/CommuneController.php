<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function switch(Request $request)
    {
        $code = $request->input('code');

        $availableCodes = config('app.available_commune_codes');

        if (!in_array($code, $availableCodes)) {
            return back()->withErrors(['code' => 'Code commune invalide.']);
        }

        session(['current_commune_code' => $code]);

        return back()->with('success', 'Commune changée avec succès.');
    }
}
