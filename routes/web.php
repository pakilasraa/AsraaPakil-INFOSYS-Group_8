<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Landing = login page
Route::get('/', function () {
    return view('auth.login');
});

// All protected pages (kailangan logged in + verified)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

    // Products (resource = full CRUD)
    Route::resource('products', ProductController::class);

    // Categories (manual CRUD)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportsController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/print', [ReportsController::class, 'printView'])->name('reports.print');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Admin Order Management Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // List orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        // View specific order
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        // Update order status
        Route::post('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

// Laravel Breeze / auth routes
require __DIR__.'/auth.php';
