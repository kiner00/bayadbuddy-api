<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'total_users' => User::role('lender')->count(),
            'total_borrowers' => Borrower::count(),
            'total_debts' => Debt::count(),
            'total_unpaid_debts' => Debt::where('status', 'pending')->count(),
        ]);
    }
}
