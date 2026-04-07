<?php

use App\Http\Controllers\Admin\CategoryOrderController;
use App\Http\Controllers\Admin\DepositReviewController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['admin.token'])->group(function () {
    Route::post('products/import', [ProductImportController::class, 'import']);
    Route::put('products/{id}', [ProductAdminController::class, 'update']);
    Route::delete('products/{id}', [ProductAdminController::class, 'destroy']);

    Route::get('deposits/pending', [DepositReviewController::class, 'pending']);
    Route::post('deposits/{id}/approve', [DepositReviewController::class, 'approve']);
    Route::post('deposits/{id}/reject', [DepositReviewController::class, 'reject']);

    Route::get('reports/sales', [ReportController::class, 'sales']);

    Route::put('settings/general', [SettingsController::class, 'updateGeneral']);

    Route::post('categories/order', [CategoryOrderController::class, 'update']);

    Route::get('users', [UserAdminController::class, 'index']);
    Route::post('users/{id}/balance', [UserAdminController::class, 'adjustBalance']);
});
