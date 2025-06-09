<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxonController;


Route::get('/', function () {
    return redirect('/plantes');
});

Route::get('/plantes', function () {
    return view('tree.plantes');
});


Route::get('/plantes/{class}', [TaxonController::class, 'plantes'])
->where('class', '^(angiospermes|gymnospermes|fougeres|mousses)$');


//Route::get('/{kingdom}', [TaxonController::class, 'kingdom'])
  //  ->where('kingdom', '^(Plantae|Animalia|Fungi)$');
