<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/create', [ProductController::class, 'create']);           
        Route::get('{product}', [ProductController::class, 'show']);    
        Route::post('/', [ProductController::class, 'store']);           
        Route::delete('{product}', [ProductController::class, 'destroy']); 
        Route::delete('/', [ProductController::class, 'bulkDelete']);   
    });
});
