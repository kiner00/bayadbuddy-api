<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum', 'role:lender', 'role:admin'], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Borrowers
    Route::post('/borrowers', [BorrowerController::class, 'store']);
    Route::get('/borrowers', [BorrowerController::class, 'index']);
    Route::get('/borrowers/{borrower}', [BorrowerController::class, 'show']);
    Route::put('/borrowers/{borrower}', [BorrowerController::class, 'update']);
    Route::delete('/borrowers/{borrower}', [BorrowerController::class, 'destroy']);

    // Debt
    Route::post('/borrowers/{borrower}/debts', [DebtController::class, 'store']);
    Route::get('/borrowers/{borrower}/debts', [DebtController::class, 'index']);
    Route::get('/debts/recent', [DebtController::class, 'recentActivity']); #TODO: test
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay']);

    // SMS
    Route::post('/debts/{borrower}/send-reminder', [ReminderController::class, 'sendReminder']); #TODO: API TEST

    // Debtors
    Route::get('/debtors', [DebtorController::class, 'index']);
    Route::get('/debts/{debt}', [DebtController::class, 'show']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']); #TODO: test

    // User
    Route::post('user/subscribe', [SubscriptionController::class, 'subscribe']);

    // Webhook
    Route::post('/payment/webhook', [PaymentWebhookController::class, 'handle']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/users', [AdminUserController::class, 'index']);
});

