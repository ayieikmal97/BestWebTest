<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProductController;

Route::get('/', [LoginController::class, 'index'])->name('index');
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::get('/product/export', [ProductController::class, 'export'])->name('product.export');
    Route::delete('/product', [ProductController::class, 'bulkDelete'])->name('product.bulk-delete');
    Route::resource('/product', ProductController::class);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

require __DIR__.'/settings.php';
