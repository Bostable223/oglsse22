<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    // Delete pending listings older than 14 days
    Listing::where('status', 'pending')
           ->where('created_at', '<', now()->subDays(14))
           ->forceDelete();
           
    // Delete read notifications older than 60 days
    (new NotificationService)->cleanupOldNotifications();
})->daily();