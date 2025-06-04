<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxonController;

Route::get('/', [TaxonController::class, 'index']);
