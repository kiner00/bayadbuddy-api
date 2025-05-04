<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtResource;
use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $unpaid = Debt::whereHas('borrower', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'pending')
            ->get();

        $paid = Debt::whereHas('borrower', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'paid')
            ->get();

        return response()->json([
            'debt_summary' => [
                'total_owed' => $unpaid->sum('amount') + $paid->sum('amount'), // ← fixed key
                'total_paid' => $paid->sum('amount'),
                'due_today' => $unpaid->filter(function ($debt) {
                    return Carbon::parse($debt->due_date)->toDateString() === now()->toDateString();
                })->sum('amount'),

                'overdue' => $unpaid->filter(function ($debt) {
                    return Carbon::parse($debt->due_date)->lt(now()->startOfDay());
                })->sum('amount'),
                'due_soon' => $unpaid->whereBetween('due_date', [now()->addDay(), now()->addDays(7)])->sum('amount'),
            ],
            'sms_credits' => [
                'used' => 12,   // Replace with actual logic or count from sms_logs
                'total' => 30,
            ]
        ]);
    }
}
