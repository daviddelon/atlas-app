<?php

use App\Http\Controllers\ObservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxonController;
use Illuminate\Support\Facades\Auth;


// Auth::loginUsingId(1); // hack

//Auth::logout();


Route::get('/', function () {
    return view('home');
});

Route::get('/plantes/angiospermes', function () {
    return redirect('/plantes/angiospermes/orchidees');
});


// Route::get('/all', [ObservationController::class,  'index']);   DEBUG


Route::get('/plantes', function () {
    return view('tree.plantes');
});

Route::get('/animaux', function () {
    return view('tree.animaux');
});

//Route::get('/plantes/{class}', [TaxonController::class, 'plantes'])
//->where('class', '^(angiospermes|gymnospermes|fougeres|mousses)$');



//Route::get('/animaux/{class}', [TaxonController::class, 'animaux'])
//->where('class', '^(mammiferes|oiseaux|insectes|reptiles)$');

//Route::get('/{kingdom}', [TaxonController::class, 'kingdom'])
  //  ->where('kingdom', '^(Plantae|Animalia|Fungi)$');

Route::get('/{kingdom}/{class}/{family?}', [TaxonController::class, 'taxaFiltre'])
    ->where('kingdom', 'plantes|animaux');
