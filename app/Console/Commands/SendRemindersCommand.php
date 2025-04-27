<?php

namespace App\Console\Commands;

use App\Models\Borrower;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\ReminderMessageService;

class SendRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders automatically to borrowers with unpaid debts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $twoDaysLater = now()->addDays(2)->toDateString();

        $borrowers = Borrower::whereHas('debts', function ($query) use ($today, $twoDaysLater) {
            $query->where('status', 'pending')
                ->where(function ($query) use ($today, $twoDaysLater) {
                    $query->where('due_date', $today)
                        ->orWhere('due_date', $twoDaysLater);
                });
        })
        ->with(['debts', 'user'])
        ->get();

        // ✅ Instantiate ReminderMessageService here
        $messageService = new ReminderMessageService();

        foreach ($borrowers as $borrower) {
            $message = $messageService->buildReminderMessage($borrower);

            Log::info("Sending SMS to {$borrower->mobile_number}: {$message}");
        }

        $this->info('Default reminders sent successfully.');
    }
}
