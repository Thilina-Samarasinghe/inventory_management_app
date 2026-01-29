<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory Items
    Route::resource('items', InventoryController::class);
    
    // Custom inventory actions
    Route::post('/items/deduct', [InventoryController::class, 'deduct'])->name('items.deduct');
    Route::get('/items/{item}/history', [InventoryController::class, 'history'])->name('items.history');
});

// Note: Fortify handles authentication routes automatically
// No need for require __DIR__.'/auth.php';