<?php

namespace App\Jobs;

use App\Contracts\SmsSenderInterface;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected string $mobileNumber;
    protected string $message;
    protected ?string $tag; // optional: 'reminder', 'otp', etc.

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $mobileNumber, string $message, ?string $tag = null)
    {
        $this->userId = $userId;
        $this->mobileNumber = $mobileNumber;
        $this->message = $message;
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsSenderInterface $smsSender): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning("SMS Job aborted: user {$this->userId} not found.");
            return;
        }

        $smsSender->send($user, $this->mobileNumber, $this->message);
    }
}
