<?php

use App\Services\Birthday\BirthdayService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('sync-birthdays', function (BirthdayService $birthdayService) {
   $birthdayService->sync();
});

Artisan::command('notify', function (BirthdayService $birthdayService) {
   $birthdayService->notifyAboutUpcomingBirthdays();
});

Schedule::command('sync-birthdays')->cron('0 2 * * 1-5'); // Every weekday at 2 a.m.
Schedule::command('notify')->dailyAt('7:00');
