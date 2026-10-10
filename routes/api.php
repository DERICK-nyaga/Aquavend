<?php

use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\Customer\CatalogController as CustomerCatalogController;
use App\Http\Controllers\Api\Customer\NotificationController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RiderDeliveryController;
use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\StockRefillController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public Customer Auth Routes
Route::prefix('customer')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/otp/request', [CustomerAuthController::class, 'requestOtp'])->middleware('throttle:5,1');
    Route::post('/otp/verify', [CustomerAuthController::class, 'verifyOtp']);
});

// Authenticated Customer Routes
Route::prefix('customer')->middleware('auth:customer')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me', [CustomerAuthController::class, 'me']);

    Route::get('/products', [CustomerCatalogController::class, 'products']);
    Route::get('/stations', [CustomerCatalogController::class, 'stations']);

    Route::get('/orders', [CustomerOrderController::class, 'index']);
    Route::post('/orders', [CustomerOrderController::class, 'store']);
    Route::get('/orders/{transaction}', [CustomerOrderController::class, 'show']);
    Route::post('/orders/{transaction}/cancel', [CustomerOrderController::class, 'cancel']);
    Route::post('/cart/quote', [CartController::class, 'quote']);

    Route::post('/wallet/top-up', [CustomerOrderController::class, 'topUpWallet']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/preference', [NotificationController::class, 'togglePreference']);
    Route::post('/fcm-token', [NotificationController::class, 'saveFcmToken']);
});

// Rider Delivery Routes
Route::middleware(['auth:sanctum'])->prefix('rider')->group(function () {
    Route::get('/deliveries', [RiderDeliveryController::class, 'index']);
    Route::get('/deliveries/{delivery}', [RiderDeliveryController::class, 'show']);
    Route::patch('/deliveries/{delivery}/status', [RiderDeliveryController::class, 'updateStatus']);
});

// Authenticated Staff/Admin Routes
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    Route::post('/customers/{customer}/repay-credit', [CustomerController::class, 'repayCredit']);
    Route::get('/products/for-sale', [ProductController::class, 'forSale']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stations', StationController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('stock-refills', StockRefillController::class)->except('update');
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('stock-transfers', StockTransferController::class)->except('update');
    Route::post('/transactions/{transaction}/confirm-pickup', [TransactionController::class, 'confirmPickup']);
    Route::post('/customers/{customer}/wallet', [CustomerController::class, 'adjustWallet']);
    Route::post('/customers/{customer}/credit-limit', [CustomerController::class, 'setCreditLimit']);
    Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel']);
});