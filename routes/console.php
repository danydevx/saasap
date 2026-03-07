<?php

use App\Jobs\CleanupExpiredPasswordResetsJob;
use App\Jobs\CleanupOldSystemErrorsJob;
use App\Jobs\CleanupOldWebhookDeliveriesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CleanupExpiredPasswordResetsJob)->hourly();
Schedule::job(new CleanupOldWebhookDeliveriesJob)->daily();
Schedule::job(new CleanupOldSystemErrorsJob)->weekly();
