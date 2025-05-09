<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Sipariş API rotaları
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::put('/orders/{order}', [OrderController::class, 'update']);
// Alternatif rotalar (sorun giderme için)
Route::get('orders/{order}', [OrderController::class, 'show']);
Route::put('orders/{order}', [OrderController::class, 'update']); 