<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // =========================================================
    // 1. Founders / General Manager Routes (Exclusive Full Edit Access)
    // =========================================================
    Route::middleware('role:Founders/General Manager')->group(function () {
        Route::get('/accounts', [\App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [\App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
        Route::get('/accounts/{id}/edit', [\App\Http\Controllers\AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
        Route::delete('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::patch('/accounts/{id}/toggle-status', [\App\Http\Controllers\AccountController::class, 'toggleStatus'])->name('accounts.toggleStatus');

        Route::get('/tracking', [\App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
    });

    // =========================================================
    // 2. Sales & Record Personnel Routes
    // =========================================================

    // Read-Only Access (Sales + Founders)
    Route::middleware('role:Sales & Record Personnel,Founders/General Manager')->group(function () {
        Route::get('/orders/create', [\App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
        Route::get('/payments/record', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
        Route::get('/sales/generate', function () { return 'Generate Sales Report'; });
    });

    // Modify Access (ONLY Sales)
    Route::middleware('role:Sales & Record Personnel')->group(function () {
        Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
        Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    });

    // =========================================================
    // 3. Production & Inventory Routes
    // =========================================================

    // Read-Only Access (Production + Craftsmen + Founders)
    Route::middleware('role:Production Manager,Craftsmen,Founders/General Manager')->group(function () {
        Route::get('/production', [\App\Http\Controllers\ProductionController::class, 'index'])->name('production.index');
        Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
    });

    // Modify Access (ONLY Production Manager)
    Route::middleware('role:Production Manager')->group(function () {
        Route::patch('/production/{order_id}', [\App\Http\Controllers\ProductionController::class, 'update'])->name('production.update');
        Route::post('/inventory', [\App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
        Route::patch('/inventory/{id}/adjust', [\App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::delete('/inventory/{id}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    // =========================================================
    // 4. Delivery Personnel Routes
    // =========================================================

    // Read-Only Access (Delivery + Founders)
    Route::middleware('role:Delivery Personnel,Founders/General Manager')->group(function () {
        Route::get('/delivery/dispatch', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('delivery.index');
    });

    // Modify Access (ONLY Delivery)
    Route::middleware('role:Delivery Personnel')->group(function () {
        Route::patch('/delivery/{order_id}', [\App\Http\Controllers\DeliveryController::class, 'update'])->name('delivery.update');
    });

});

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});
