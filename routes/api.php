<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\EnclosureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Nebula9 API is healthy',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.health');

Route::apiResource('enclosures', EnclosureController::class);
Route::apiResource('animals', AnimalController::class);

Route::post('animals/{animal}/transfer', [AnimalController::class, 'transfer'])->name('animals.transfer');
