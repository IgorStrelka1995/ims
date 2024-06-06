<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('products', \App\Http\Controllers\Api\v1\ProductController::class);
Route::apiResource('stocks', \App\Http\Controllers\Api\v1\StockController::class)->only(['index', 'show']);
Route::apiResource('audits', \App\Http\Controllers\Api\v1\AuditController::class)->only(['index', 'show']);

Route::post("stocks/in/{product}", [\App\Http\Controllers\Api\v1\StockController::class, "in"]);
Route::post("stocks/out/{product}", [\App\Http\Controllers\Api\v1\StockController::class, "out"]);
