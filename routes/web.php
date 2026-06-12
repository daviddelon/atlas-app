<?php

use App\Http\Controllers\ObservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxonController;
use Illuminate\Support\Facades\Auth;


//Auth::loginUsingId(1); // hack

Auth::logout();


Route::get('/', function () {
    $location = config('app.default_commune_location');
    return redirect("/$location");
});

// Route supprimée - la redirection vers la famille la plus observée est gérée dans TaxonController


// Route::get('/all', [ObservationController::class,  'index']);   DEBUG


Route::get('/plantes', function () {
    $location = config('app.default_commune_location');
    return redirect("/$location/plantes/angiospermes");
});


/*
Route::get('/animaux', function () {
    return view('tree.animaux');
});
*/

//Route::get('/plantes/{class}', [TaxonController::class, 'plantes'])
//->where('class', '^(angiospermes|gymnospermes|fougeres|mousses)$');



//Route::get('/animaux/{class}', [TaxonController::class, 'animaux'])
//->where('class', '^(mammiferes|oiseaux|insectes|reptiles)$');

//Route::get('/{kingdom}', [TaxonController::class, 'kingdom'])
  //  ->where('kingdom', '^(Plantae|Animalia|Fungi)$');

Route::get('/{location}', function (string $location) {
    $communeCode = config('app.default_commune_code');
    $communeName = \App\Models\Commune::where('code', $communeCode)->value('nom') ?? 'Atlas';
    return view('welcome', compact('communeName', 'location'));
})->where('location', config('app.default_commune_location'));

Route::get('/{location}/{kingdom}/{class}/{family?}', [TaxonController::class, 'taxaFiltre'])
    ->where('location', config('app.default_commune_location'))
    ->where('kingdom', 'plantes|animaux')
    ->where('class', 'angiospermes|gymnospermes|fougeres|mousses');
