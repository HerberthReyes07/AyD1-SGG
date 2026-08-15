<?php

use App\Console\Commands\ProcessMembershipTasks;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Membership scheduled tasks
|--------------------------------------------------------------------------
|
| Runs every day at midnight. Handles time-dependent membership operations:
|  - Automatic reactivation of frozen memberships whose estimated_reactivation_date
|    has been reached.
|
| Future tasks (expiration marking, notifications, etc.) should be added
| inside ProcessMembershipTasks, not here.
|
*/
Schedule::command(ProcessMembershipTasks::class)->dailyAt('00:00');
