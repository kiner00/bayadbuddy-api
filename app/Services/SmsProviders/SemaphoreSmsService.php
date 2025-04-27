<?php

namespace App\Services\SmsProviders;

use App\Contracts\SmsSenderInterface;
use Illuminate\Support\Facades\Http;

class SemaphoreSmsService implements SmsSenderInterface
{
    protected string $apiKey;
    protected string $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name');
    }

    public function send(string $mobileNumber, string $message): bool
    {
        $response = Http::asForm()->post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $this->apiKey,
            'number' => $mobileNumber,
            'message' => $message,
            'sendername' => $this->senderName,
        ]);

        if ($response->successful()) {
            return true;
        }

        \Log::error('Semaphore SMS Failed', [
            'response' => $response->body(),
        ]);

        return false;
    }
}
