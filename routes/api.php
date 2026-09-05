<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\StockRefillController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\StockTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\CatalogController as CustomerCatalogController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('customer')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/otp/request', [CustomerAuthController::class, 'requestOtp']);
    Route::post('/otp/verify', [CustomerAuthController::class, 'verifyOtp']);
});

Route::prefix('customer')->middleware('auth:customer')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me', [CustomerAuthController::class, 'me']);

    Route::get('/products', [CustomerCatalogController::class, 'products']);
    Route::get('/stations', [CustomerCatalogController::class, 'stations']);

    Route::get('/orders', [CustomerOrderController::class, 'index']);
    Route::post('/orders', [CustomerOrderController::class, 'store']);
    Route::get('/orders/{transaction}', [CustomerOrderController::class, 'show']);

    Route::post('/wallet/top-up', [CustomerOrderController::class, 'topUpWallet']);
});

Route::middleware('auth:sanctum')->group(function () {
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
    Route::apiResource('expenses', ExpenseController::class)->except('update');
    Route::post('/customers/{customer}/repay-credit', [CustomerController::class, 'repayCredit']);
    Route::apiResource('stock-transfers', StockTransferController::class)->except('update');
    Route::post('/transactions/{transaction}/confirm-pickup', [TransactionController::class, 'confirmPickup']);
    Route::post('/customers/{customer}/wallet', [CustomerController::class, 'adjustWallet']);
    Route::post('/customers/{customer}/credit-limit', [CustomerController::class, 'setCreditLimit']);
    Route::post('/orders/{transaction}/cancel', [CustomerOrderController::class, 'cancel']);
    Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel']);
});