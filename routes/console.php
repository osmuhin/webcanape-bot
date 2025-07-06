<?php

use App\Services\Birthday\BirthdayService;
use Illuminate\Support\Facades\Artisan;

Artisan::command('sync-birthdays', function (BirthdayService $birthdayService) {
   $birthdayService->sync();
});
