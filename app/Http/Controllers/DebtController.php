<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebtRequest;
use App\Http\Resources\DebtDetailResource;
use App\Http\Resources\DebtResource;
use App\Models\Borrower;
use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function show(Debt $debt)
    {
        $debt->load(['borrower', 'payments']);

        return new DebtResource($debt);
    }

    public function store(DebtRequest $request, Borrower $borrower)
    {
        if ($request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $debt = Debt::create([
            'borrower_id' => $borrower->id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'interest_type' => $request->interest_type,
            'interest_value' => $request->interest_value,
        ]);

        $debt->load('borrower', 'payments');

        return new DebtResource($debt);
    }

    public function index(Request $request, Borrower $borrower)
    {
        if ($request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $debts = $borrower->debts()
            ->with('payments')
            ->latest()
            ->get();

        return DebtResource::collection($debts);
    }

    public function pay(Request $request, Debt $debt)
    {
        if ($request->user()->id !== $debt->borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $debt->update([
            'status' => 'paid',
        ]);

        return new DebtResource($debt);
    }

    public function recentActivity(Request $request)
    {
        $user = $request->user();

        $debts = Debt::with('borrower')
            ->whereHas('borrower', fn ($q) => $q->where('user_id', $user->id))
            ->latest('due_date')
            ->limit(10)
            ->get();

        return DebtResource::collection($debts);
    }
}
