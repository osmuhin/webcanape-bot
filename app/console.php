<?php

use App\Services\Birthday\Birthday;
use App\Services\Telegram\Telegram;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('sync-birthdays', function (Birthday $birthday) {
	dispatch(fn () => $birthday->makeSynchronizer()->sync());
});

Artisan::command('notify', function (Birthday $birthday) {
	$birthday->makeNotifier()->notifyAboutUpcomingBirthdays();
});

Artisan::command('tg:webhook:setup', function (Telegram $telegram) {
	$telegram->setupWebhook();

	$this->info("Telegram webhook is set on URL {$telegram->getWebhookUrl(abs: true)}");
});

Artisan::command('tg:webhook:delete', function (Telegram $telegram) {
	$telegram->getSdk()->deleteWebhook();

	$this->info("Telegram webhook was removed successfuly");
});

// Schedule::command('sync-birthdays')->dailyAt('01:20');
// Schedule::command('notify')->dailyAt('7:00');
