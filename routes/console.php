<?php

use App\Services\Birthday\BirthdayService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('sync-birthdays', function (BirthdayService $birthdayService) {
	dispatch(fn () => $birthdayService->sync());
});

Artisan::command('notify', function (BirthdayService $birthdayService) {
	$birthdayService->notifyAboutUpcomingBirthdays();
});

Schedule::command('sync-birthdays')->dailyAt('01:20');
Schedule::command('notify')->dailyAt('7:00');
