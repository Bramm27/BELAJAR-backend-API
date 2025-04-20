<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product', 'index');
        Route::get('/product/{id}', 'show');
        Route::post('/product', 'store');  
        Route::put('/product/{id}', 'update');
        Route::delete('/product/{id}', 'destroy'); 
    });

    // Route untuk mendapatkan user yang sedang login
    Route::get('user', function (Request $request) {
        return $request->user();
    })->name('user');
});