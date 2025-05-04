<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'payment_reference' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'status' => ['required', 'string'],
        ]);

        if ($request->input('status') !== 'paid') {
            return response()->json(['message' => 'Payment not completed.'], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Activate Subscription
        $user->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => Carbon::now()->addDays(30),
        ]);

        return response()->json([
            'message' => 'Subscription activated successfully.',
        ]);
    }
}
