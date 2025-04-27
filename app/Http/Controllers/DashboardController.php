<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $unpaidDebts = Debt::whereHas('borrower', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->get();

        $paidDebts = Debt::whereHas('borrower', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'paid')->get();

        return response()->json([
            'unpaid_count' => $unpaidDebts->count(),
            'paid_count' => $paidDebts->count(),
            'total_unpaid_amount' => $unpaidDebts->sum('amount'),
        ]);
    }
}
