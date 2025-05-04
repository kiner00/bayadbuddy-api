<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        // Simulate sending request to Payment Gateway (Multipay)
        $response = Http::post('https://api.payment-gateway.com/checkout', [
            'amount' => 99, // ₱99 subscription
            'currency' => 'PHP',
            'description' => 'BayadBuddy Monthly Subscription',
            'customer_email' => $request->user()->email,
            'callback_url' => config('app.url') . '/api/payment/webhook', // Our webhook URL
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to create payment session.'], 500);
        }

        return response()->json([
            'checkout_url' => $response->json('checkout_url'),
        ], 201);
    }
}
