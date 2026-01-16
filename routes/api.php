<?php

use App\Http\Controllers\EnclosureController;
use Illuminate\Support\Facades\Route;

Route::apiResource('enclosures', EnclosureController::class);
