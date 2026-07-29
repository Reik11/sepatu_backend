<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Redirect home route to Admin Dashboard for easy assessment
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Panel Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard Stats
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // CRUD Shoes (Produk Sepatu)
    Route::prefix('shoes')->name('shoes.')->group(function () {
        Route::get('/', [AdminController::class, 'shoesIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'shoesCreate'])->name('create');
        Route::post('/store', [AdminController::class, 'shoesStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'shoesEdit'])->name('edit');
        Route::post('/{id}/update', [AdminController::class, 'shoesUpdate'])->name('update');
        Route::post('/{id}/delete', [AdminController::class, 'shoesDestroy'])->name('destroy');
    });

    // Transactions Management
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [AdminController::class, 'transactionsIndex'])->name('index');
        Route::get('/{id}', [AdminController::class, 'transactionsShow'])->name('show');
        Route::post('/{id}/status', [AdminController::class, 'transactionsUpdateStatus'])->name('update-status');
    });
});
