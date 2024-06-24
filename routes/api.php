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

Route::post('login', [\App\Http\Controllers\Api\v1\AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('products', \App\Http\Controllers\Api\v1\ProductController::class);
    Route::apiResource('stocks', \App\Http\Controllers\Api\v1\StockController::class)->only(['index', 'show']);
    Route::apiResource('audits', \App\Http\Controllers\Api\v1\AuditController::class)->only(['index', 'show']);

    Route::post("stocks/in/{product}", [\App\Http\Controllers\Api\v1\StockController::class, "in"]);
    Route::post("stocks/out/{product}", [\App\Http\Controllers\Api\v1\StockController::class, "out"]);

    Route::post('user/register', [\App\Http\Controllers\Api\v1\UserController::class, 'register']);
    Route::post('logout', [\App\Http\Controllers\Api\v1\AuthController::class, 'logout']);
});
