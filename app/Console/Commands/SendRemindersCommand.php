<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsJob;
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
                ->whereIn('due_date', [$today, $twoDaysLater]);
        })
        ->with(['debts', 'user'])
        ->get();

        $messageService = new ReminderMessageService();

        foreach ($borrowers as $borrower) {
            $user = $borrower->user;

            if (!$user) {
                Log::warning("Borrower {$borrower->id} has no user assigned.");
                continue;
            }

            $message = $messageService->buildReminderMessage($borrower);

            SendSmsJob::dispatch(
                $user->id,
                $borrower->mobile_number,
                $message,
                'reminder'
            );
        }

        $this->info('All SMS reminder jobs have been queued.');
    }
}
