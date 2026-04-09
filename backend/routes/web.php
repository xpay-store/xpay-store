<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'status' => 'ok',
    ]);
});

Route::get('/admin', [\App\Http\Controllers\AdminUi\AdminAuthController::class, 'showLogin']);
Route::post('/admin/login', [\App\Http\Controllers\AdminUi\AdminAuthController::class, 'login']);
Route::post('/admin/logout', [\App\Http\Controllers\AdminUi\AdminAuthController::class, 'logout']);

Route::middleware(['admin.session'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminUi\AdminDashboardController::class, 'index']);

    // Admin UI JSON endpoints (session-protected)
    Route::prefix('/admin/ui')->group(function () {
        Route::get('stats', [\App\Http\Controllers\AdminUi\AdminStatsController::class, 'overview']);
        Route::get('users', [\App\Http\Controllers\AdminUi\AdminUsersController::class, 'index']);
        Route::get('deposits/pending', [\App\Http\Controllers\AdminUi\AdminDepositsController::class, 'pending']);
        Route::post('deposits/{id}/approve', [\App\Http\Controllers\AdminUi\AdminDepositsController::class, 'approve']);
        Route::post('deposits/{id}/reject', [\App\Http\Controllers\AdminUi\AdminDepositsController::class, 'reject']);
        Route::get('products', [\App\Http\Controllers\AdminUi\AdminProductsController::class, 'index']);
        Route::put('products/{id}', [\App\Http\Controllers\AdminUi\AdminProductsController::class, 'update']);
        Route::delete('products/{id}', [\App\Http\Controllers\AdminUi\AdminProductsController::class, 'destroy']);
    });
});
