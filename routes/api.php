<?php

use App\Http\Controllers\RageAnalysisController;
use Illuminate\Support\Facades\Route;

Route::post('/rage-analyses', [RageAnalysisController::class, 'store']);

