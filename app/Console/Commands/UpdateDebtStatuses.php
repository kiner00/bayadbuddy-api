<?php

namespace App\Console\Commands;

use App\Jobs\UpdateDebtStatusesJob;
use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateDebtStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debts:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update overdue debts to status "overdue" if past due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateDebtStatusesJob::dispatch();
        $this->info('Debt status update job dispatched.');
    }
}
