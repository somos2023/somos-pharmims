<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\LoginAttemptController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\ChatAppController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
// use App\Http\Controllers\SellProductController;
// use App\Http\Controllers\BrandController;
// use App\Http\Controllers\UnitController;
// use App\Http\Controllers\DiscountController;
// use App\Http\Controllers\ProductSupplyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth-user', [UserController::class, 'authUser']); 
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::post('/register', [AuthController::class, 'register']);
    Route::get('/dashboard', [DashboardController::class, 'index']); 
    Route::get('/get-notif', [DashboardController::class, 'getNotif']); 
    Route::put('/change-notif-status/{id}', [DashboardController::class, 'changeNotifStatus']); 
    Route::get('/send-mail', [MailController::class, 'sendSampleMail']);
    // Route::post('/send-sms', [NotificationController::class, 'sendSmsNotificaition']);

    // Route::put('/lock/{id}', [UserController::class, 'lockUser']); 
    Route::put('/change-my-info/{id}', [UserController::class, 'profile']); 
    Route::put('/change-password/{id}', [UserController::class, 'changePassword']); 
    Route::resource('/user', UserController::class); 
    Route::post('/change-user-status/{id}', [UserController::class, 'changeStatus']); 

    Route::resource('/product', ProductController::class); 
    // Route::resource('/chat', ChatController::class); 
    // Route::get('/get-chat/{id}', [ChatController::class, "getData"]); 

    Route::resource('/chat-app', ChatAppController::class); 
    Route::post('/get-messages', [ChatAppController::class, 'getMessages']); 

    Route::get('/supplier-product', [ProductController::class, "supplierProduct"]); 
    Route::resource('/cart', CartController::class); 
    Route::resource('/order', OrderController::class); 
    Route::post('/buy-now', [OrderController::class, "buyNow"]); 

    Route::get('/my-order', [OrderController::class, "getMyOrder"]); 
    Route::get('/shop-order', [OrderController::class, "getShopOrder"]); 

    Route::get('/stocks', [StockController::class, "getStocks"]); 

    Route::post('/last-login', [UserController::class, 'updateLogin']); 
    // Route::resource('/brand', BrandController::class); 
    // Route::resource('/unit', UnitController::class); 
    // Route::resource('/supply', ProductSupplyController::class); 
    // Route::resource('/discount', DiscountController::class);\

    Route::resource('/system', SystemSettingController::class);

    Route::resource('/transaction', TransactionController::class);

    Route::get('/order-sales', [ReportController::class, 'getOrderSalesData']);
    Route::get('/transaction-sales', [ReportController::class, 'getTransactionSalesData']);
    Route::post('/order-sales', [ReportController::class, 'getOrderSalesData']);
    Route::post('/transaction-sales', [ReportController::class, 'getTransactionSalesData']);

    Route::get('download-excel/{model}', [ExportController::class, 'exportExcel']);
    Route::get('download-pdf/{model}', [ExportController::class, 'downloadPDF']);

});

Route::post('/login-attempt/{id}', [LoginAttemptController::class, 'updateCount']);
Route::resource('/login-attempt', LoginAttemptController::class);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [APIController::class, 'forget_pass']);
Route::post('/reset-password', [APIController::class, 'reset_pass']);
Route::get('/system-info', [SystemSettingController::class, 'index']); 