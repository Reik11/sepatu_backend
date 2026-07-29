<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// RESTful JSON API Routes for Flutter Mobile Client
Route::prefix('shoes')->group(function () {
    Route::get('/', [ApiController::class, 'getShoes']);
    Route::get('/{id}', [ApiController::class, 'getShoeDetail']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiController::class, 'register']);
    Route::post('/login', [ApiController::class, 'login']);
});

Route::prefix('transactions')->group(function () {
    Route::post('/', [ApiController::class, 'createTransaction']);
    Route::get('/user/{userId}', [ApiController::class, 'getUserTransactions']);
});
