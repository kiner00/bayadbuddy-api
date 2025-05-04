<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('debts:update-status')->dailyAt('00:00');
Schedule::command('send:reminders')->dailyAt('08:00');
