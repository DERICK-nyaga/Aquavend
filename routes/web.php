<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\TransactionWebController;
use App\Http\Controllers\StockRefillWebController;
use App\Http\Controllers\StationWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockTransferWebController;
use App\Http\Controllers\NotificationWebController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierWebController;
use App\Http\Controllers\ExpenseWebController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('stations', StationWebController::class)->except(['show']);
    Route::resource('products', ProductWebController::class)->except(['show']);
    Route::resource('customers', CustomerWebController::class)->except(['show']);
    Route::resource('transactions', TransactionWebController::class)->except(['edit', 'update']);
    Route::resource('stock-refills', StockRefillWebController::class)->except(['edit', 'update', 'show']);
    Route::resource('suppliers', SupplierWebController::class);
    Route::resource('expenses', ExpenseWebController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('stock-transfers', StockTransferWebController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/notifications', [NotificationWebController::class, 'index'])->name('notifications');
    Route::post('/transactions/{transaction}/confirm-pickup', [TransactionWebController::class, 'confirmPickup'])
    ->name('transactions.confirm-pickup');
    Route::post('/customers/{customer}/wallet', [CustomerWebController::class, 'adjustWallet'])->name('customers.wallet');
    Route::post('/customers/{customer}/credit-limit', [CustomerWebController::class, 'setCreditLimit'])->name('customers.credit-limit');
    Route::post('/transactions/{transaction}/cancel', [TransactionWebController::class, 'cancel'])
    ->name('transactions.cancel');
});