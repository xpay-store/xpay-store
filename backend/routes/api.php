<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserProfileController;
use Bots\DepositBotWebhook;
use Bots\StoreBotWebhook;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/telegram/deposit/{secret}', DepositBotWebhook::class);
Route::post('/webhooks/telegram/store/{secret}', StoreBotWebhook::class);

Route::middleware(['telegram.webapp'])->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'show']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::post('/order/create', [OrderController::class, 'create']);
    Route::get('/order/status/{order_id}', [OrderController::class, 'status']);
    Route::get('/orders/my', [OrderController::class, 'mine']);

    Route::post('/deposit/create', [DepositController::class, 'create']);
    Route::get('/deposit/history', [DepositController::class, 'history']);
});
