<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsSenderInterface;
use App\Models\User;
use App\Services\PhoneNumberFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService implements SmsSenderInterface
{
    protected string $apiUrl;
    protected string $token;
    protected string $senderId;
    protected PhoneNumberFormatter $formatter;

    public function __construct(PhoneNumberFormatter $formatter)
    {
        $this->formatter = $formatter;
        $this->apiUrl = config('services.philsms.api_url');
        $this->token = config('services.philsms.api_key');
        $this->senderId = config('services.philsms.sender_id', 'BayadBuddy'); // Optional default
    }

    public function send(User $user, string $mobileNumber, string $message): bool
    {
        if (!$user->canSendSms()) {
            Log::warning("User {$user->id} exceeded SMS quota.");
            return false;
        }

        $formatted = $this->formatter->normalizeTo639($mobileNumber);

        $response = Http::withToken($this->token)->post($this->apiUrl, [
            'recipient' => $formatted,
            'sender_id' => $this->senderId,
            'type' => 'plain',
            'message' => $message,
        ]);

        if ($response->successful()) {
            $user->incrementSmsUsage();
        }

        return $response->successful();
    }
}
