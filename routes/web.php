<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObservationController;

Route::get('/', [ObservationController::class, 'index']);
