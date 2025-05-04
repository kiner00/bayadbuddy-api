<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtorResource;
use App\Models\Borrower;
use App\Models\Debt;
use Illuminate\Http\Request;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $debts = Debt::with(['borrower'])
            ->whereHas('borrower', fn ($q) => $q->where('user_id', $user->id))
            ->latest('due_date')
            ->get();

        return DebtorResource::collection($debts);
    }
}
