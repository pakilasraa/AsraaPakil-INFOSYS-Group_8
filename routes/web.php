<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AiController;
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

    // AI Assistant
    Route::get('/ai', [AiController::class, 'index'])->name('ai.index');
    Route::post('/ai/chat', [AiController::class, 'adminChat'])->name('ai.admin-chat');

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

// Fallback for Flutter Web (SPA)
Route::fallback(function () {
    // If the request expects JSON, return 404.
    if (request()->expectsJson()) {
        return response()->json(['message' => 'Not Found.'], 404);
    }
    // Otherwise serve the Flutter app
    // Ensure public/index.html exists after running `flutter build web`
    return file_get_contents(public_path('index.html'));
});
