<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsSenderInterface;
use App\Services\PhoneNumberFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService implements SmsSenderInterface
{
    protected string $apiUrl = 'https://app.philsms.com/api/v3/sms/send';
    protected string $token;
    protected string $senderId;
    protected PhoneNumberFormatter $formatter;

    public function __construct(PhoneNumberFormatter $formatter)
    {
        $this->formatter = $formatter;
        $this->token = config('services.philsms.api_key');
        $this->senderId = config('services.philsms.sender_id', 'BayadBuddy'); // Optional default
    }

    public function send(string $mobileNumber, string $message): bool
    {
        $normalized = $this->formatter->normalizeTo639($mobileNumber);

        if (!$normalized) {
            Log::error('Invalid mobile number format', ['input' => $mobileNumber]);
            return false;
        }

        $response = Http::withToken($this->token)
            ->acceptJson()
            ->post($this->apiUrl, [
                'recipient' => $normalized,
                'sender_id' => $this->senderId,
                'type' => 'plain',
                'message' => $message,
            ]);

        Log::info('PhilSMS Response', [
            'recipient' => $normalized,
            'response' => $response->json(),
        ]);

        return $response->successful();
    }
}
