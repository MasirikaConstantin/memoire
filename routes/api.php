<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
use App\Http\Controllers\Api\PlateController;

Route::prefix('v1')->group(function () {
    Route::post('/check-plate', [PlateController::class, 'checkPlate']);
    Route::get('/plate/{plateNumber}/violations', [PlateController::class, 'getPlateViolations']);
});