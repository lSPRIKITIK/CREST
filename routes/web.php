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

    // Global Dashboard (Everyone can access their own version of the dashboard)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
            ->name('dashboard');

    // 1. Founders / General Manager Routes
    Route::middleware('role:Founders/General Manager')->group(function () {
        Route::get('/accounts', [\App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
        Route::post('/accounts', [\App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');

        // Add these three lines:
        Route::get('/accounts/{id}/edit', [\App\Http\Controllers\AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
        Route::delete('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::patch('/accounts/{id}/toggle-status', [\App\Http\Controllers\AccountController::class, 'toggleStatus'])->name('accounts.toggleStatus');
        Route::get('/tracking', [\App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
    });

    // 2. Sales & Record Personnel Routes
    Route::middleware('role:Sales & Record Personnel')->group(function () {
        Route::get('/orders/create', [\App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');

        // Update these two lines:
        Route::get('/payments/record', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');

        Route::get('/sales/generate', function () { return 'Generate Sales Report'; });
    });

    // 3. Production Manager & Craftsmen Routes
    Route::middleware('role:Production Manager,Craftsmen')->group(function () {
        Route::get('/production', [\App\Http\Controllers\ProductionController::class, 'index'])->name('production.index');
        Route::patch('/production/{order_id}', [\App\Http\Controllers\ProductionController::class, 'update'])->name('production.update');

        // Inventory Management Routes
        Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [\App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
        Route::patch('/inventory/{id}/adjust', [\App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::delete('/inventory/{id}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    // 4. Delivery Personnel Routes
    Route::middleware('role:Delivery Personnel')->group(function () {
        Route::get('/delivery/dispatch', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('delivery.index');
        Route::patch('/delivery/{order_id}', [\App\Http\Controllers\DeliveryController::class, 'update'])->name('delivery.update');
    });
});

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});
