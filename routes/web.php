<?php

use App\Http\Controllers\ObservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxonController;


Route::get('/', function () {
    return redirect('/plantes');
});


// Route::get('/all', [ObservationController::class,  'index']);   DEBUG



Route::get('/plantes', function () {
    return view('tree.plantes');
});

Route::get('/animaux', function () {
    return view('tree.animaux');
});

Route::get('/plantes/{class}', [TaxonController::class, 'plantes'])
->where('class', '^(angiospermes|gymnospermes|fougeres|mousses)$');



Route::get('/animaux/{class}', [TaxonController::class, 'animaux'])
->where('class', '^(mammiferes|oiseaux|insectes|reptiles)$');

//Route::get('/{kingdom}', [TaxonController::class, 'kingdom'])
  //  ->where('kingdom', '^(Plantae|Animalia|Fungi)$');
