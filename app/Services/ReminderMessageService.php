<?php

namespace App\Services;

use App\Models\Borrower;

class ReminderMessageService
{
    public function buildReminderMessage(Borrower $borrower): string
    {
        $borrowerFullName = trim("{$borrower->name}");
        $lenderFullName = trim("{$borrower->user->first_name} {$borrower->user->last_name}");

        return "Good day, {$borrowerFullName}. This is BayadBuddy. We would like to remind you that you have an outstanding balance with {$lenderFullName}. Please settle your payment at your earliest convenience. Thank you for trusting BayadBuddy.";
    }
}
