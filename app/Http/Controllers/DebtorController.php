<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtorResource;
use App\Models\Borrower;
use Illuminate\Http\Request;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $debtors = Borrower::where('user_id', $user->id)
            ->whereHas('debts', function ($query) {
                $query->where('status', 'pending');
            })
            ->withCount(['debts as unpaid_debts_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->latest()
            ->get();

        return DebtorResource::collection($debtors);
    }
}
