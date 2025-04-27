<?php

namespace App\Jobs;

use App\Contracts\SmsSenderInterface;
use App\Models\Borrower;
use App\Services\ReminderMessageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsReminderJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected Borrower $borrower;

    public int $tries = 3; // ✅ Try 3 times max
    public int $backoff = 30; // ✅ Wait 30 seconds before retry

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsSenderInterface $smsSender): void
    {
        $messageService = new ReminderMessageService();
        $message = $messageService->buildReminderMessage($this->borrower);

        $smsSender->send($this->borrower->mobile_number, $message);
    }
}
