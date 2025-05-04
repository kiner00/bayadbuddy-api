<?php

namespace App\Http\Controllers;

use App\Contracts\SmsSenderInterface;
use App\Models\Borrower;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    public function sendReminder(Request $request, $debtId, SmsSenderInterface $smsService)
    {
        $debt = Debt::with('borrower')->findOrFail($debtId);
        $borrower = $debt->borrower;

        if (!$borrower || $request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $message = $validated['message']
            ?? "Hi {$borrower->name}, your ₱" . number_format($debt->amount, 0) . " debt is due on {$debt->due_date}. Please settle it soon. Thank you!";

        $sent = $smsService->send($borrower->mobile_number, $message);

        return response()->json([
            'message' => $sent ? 'SMS reminder sent successfully.' : 'SMS failed to send.',
        ]);
    }
}
