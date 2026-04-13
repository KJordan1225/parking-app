<?php

use App\Http\Controllers\Api\v1\PlaceController;
use App\Http\Controllers\Api\v1\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('places', [PlaceController::class, 'index']);
Route::post('book/reservation', [ReservationController::class, 'store']);
Route::put('cancel/{reservation}/reservation', [ReservationController::class, 'cancel']);
Route::put('start/{reservation}/parking', [ReservationController::class, 'startParking']);
Route::put('end/{reservation}/parking', [ReservationController::class, 'endParking']);

