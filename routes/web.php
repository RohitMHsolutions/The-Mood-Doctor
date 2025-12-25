<?php

use App\Http\Controllers\RageAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('rage');
});

Route::get('/history', function () {
    return view('history');
});

Route::post('/rage/analyze', [RageAnalysisController::class, 'analyze']);
Route::post('/rage/save', [RageAnalysisController::class, 'save']);
Route::get('/rage/history', [RageAnalysisController::class, 'history']);