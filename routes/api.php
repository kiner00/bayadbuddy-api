<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DebtController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
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
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay']);
});
