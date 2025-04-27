<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function sendSms(Request $request, Borrower $borrower)
    {
        if ($request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $message = $validated['message'] ?? "Hi {$borrower->name}, you still have outstanding balances with us. Please settle soon. Thank you!";

        // Simulate SMS sending (later integrate actual SMS gateway)
        \Log::info("Sending SMS to {$borrower->mobile_number}: {$message}");

        return response()->json([
            'message' => 'SMS reminder sent successfully.',
        ]);
    }
}
