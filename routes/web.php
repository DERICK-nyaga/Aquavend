<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryWebController;
use App\Http\Controllers\ExpenseWebController;
use App\Http\Controllers\MotorbikeController;
use App\Http\Controllers\NotificationWebController;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestockOrderWebController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\StationStockWebController;
use App\Http\Controllers\StationWebController;
use App\Http\Controllers\StockRefillWebController;
use App\Http\Controllers\StockTransferWebController;
use App\Http\Controllers\SupplierWebController;
use App\Http\Controllers\TransactionWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store']);

    Route::get('/staff/login', [StaffLoginController::class, 'create'])->name('staff.login');
    Route::post('/staff/login', [StaffLoginController::class, 'store']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Shared Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboards & Reports
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/notifications', [NotificationWebController::class, 'index'])->name('notifications');

    // Stations
    Route::resource('stations', StationWebController::class);
    Route::get('/stations/{station}/stock', [StationStockWebController::class, 'index'])->name('stations.stock');
    Route::post('/stations/{station}/stock', [StationStockWebController::class, 'update'])->name('stations.stock.update');

    // Inventory & Catalog Management
    Route::resource('products', ProductWebController::class)->except(['show']);
    Route::resource('stock-refills', StockRefillWebController::class)->except(['edit', 'update', 'show']);
    Route::resource('stock-transfers', StockTransferWebController::class)->only(['index', 'create', 'store', 'destroy']);
    
    // Restock Orders
    Route::resource('restock-orders', RestockOrderWebController::class);
    Route::post('/restock-orders/{restockOrder}/receive', [RestockOrderWebController::class, 'markAsReceived'])->name('restock-orders.receive');

    // Customers & Financial Management
    Route::resource('customers', CustomerWebController::class)->except(['show']);
    Route::post('/customers/{customer}/credit-limit', [CustomerWebController::class, 'setCreditLimit'])->name('customers.credit-limit');

    // Transactions & Deliveries
    Route::resource('transactions', TransactionWebController::class)->except(['edit', 'update']);
    Route::post('/transactions/{transaction}/confirm-pickup', [TransactionWebController::class, 'confirmPickup'])->name('transactions.confirm-pickup');
    Route::post('/transactions/{transaction}/dispatch', [TransactionWebController::class, 'confirmDispatch'])->name('transactions.dispatch');
    Route::post('/transactions/{transaction}/deliver', [TransactionWebController::class, 'markDelivered'])->name('transactions.deliver');
    Route::post('/transactions/{transaction}/cancel', [TransactionWebController::class, 'cancel'])->name('transactions.cancel');

    Route::get('/deliveries', [DeliveryWebController::class, 'index'])->name('deliveries.index');
    Route::patch('/deliveries/{delivery}/assign', [DeliveryWebController::class, 'assignDriver'])->name('deliveries.assign');
    Route::patch('/deliveries/{delivery}/status', [DeliveryWebController::class, 'updateStatus'])->name('deliveries.updateStatus');

    /*
    |--------------------------------------------------------------------------
    | Staff Management (Admin & Director Roles)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,director')->group(function () {
        Route::get('/staff/dashboard', [StaffManagementController::class, 'dashboard'])->name('staff.dashboard');
        Route::resource('staff', StaffManagementController::class);

        // Staff Assignments & Actions
        Route::get('/staff/{staff}/assign-station', [StaffManagementController::class, 'showAssignStationForm'])->name('staff.assign-station.form');
        Route::match(['post', 'patch'], '/staff/{staff}/assign-station', [StaffManagementController::class, 'assignStation'])->name('staff.assign-station');
        Route::match(['post', 'patch'], '/staff/{staff}/approve', [StaffManagementController::class, 'approve'])->name('staff.approve');
        Route::match(['post', 'patch'], '/staff/{staff}/suspend', [StaffManagementController::class, 'suspend'])->name('staff.suspend');
        Route::match(['post', 'patch'], '/staff/{staff}/dismiss', [StaffManagementController::class, 'dismiss'])->name('staff.dismiss');
        Route::match(['post', 'patch'], '/staff/{staff}/reinstate', [StaffManagementController::class, 'reinstate'])->name('staff.reinstate');

        // Staff Leave Management
        Route::post('/staff/{staff}/leave', [StaffManagementController::class, 'placeOnLeave'])->name('staff.leave');
        Route::match(['post', 'patch'], '/staff/{staff}/end-leave', [StaffManagementController::class, 'endLeave'])->name('staff.end-leave');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only Operations
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('suppliers', SupplierWebController::class);
        Route::resource('expenses', ExpenseWebController::class);
        Route::resource('motorbikes', MotorbikeController::class);
        Route::post('/customers/{customer}/wallet', [CustomerWebController::class, 'adjustWallet'])->name('customers.wallet');
    });
});