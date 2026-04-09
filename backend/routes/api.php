<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserProfileController;
use Bots\DepositBotWebhook;
use Bots\StoreBotWebhook;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// =============================================
// Health Check for Render
// =============================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'XPayStore Backend'
    ], 200);
});

// =============================================
// Telegram Bot Webhooks
// =============================================
Route::post('/webhooks/telegram/deposit/{secret}', DepositBotWebhook::class);
Route::post('/webhooks/telegram/store/{secret}', StoreBotWebhook::class);

// =============================================
// Protected Routes (Telegram WebApp Authentication)
// =============================================
Route::middleware(['telegram.webapp'])->group(function () {

    // User Profile
    Route::get('/user/profile', [UserProfileController::class, 'show']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Orders
    Route::post('/order/create', [OrderController::class, 'create']);
    Route::get('/order/status/{order_id}', [OrderController::class, 'status']);
    Route::get('/orders/my', [OrderController::class, 'mine']);

    // Deposits
    Route::post('/deposit/create', [DepositController::class, 'create']);
    Route::get('/deposit/history', [DepositController::class, 'history']);
});
