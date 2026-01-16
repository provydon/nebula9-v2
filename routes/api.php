<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\EnclosureController;
use Illuminate\Support\Facades\Route;

Route::apiResource('enclosures', EnclosureController::class);

Route::apiResource('animals', AnimalController::class);
Route::post('animals/{animal}/transfer', [AnimalController::class, 'transfer'])->name('animals.transfer');
