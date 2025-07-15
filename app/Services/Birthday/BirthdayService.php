<?php

namespace App\Services\Birthday;

use App\Models\User;
use App\Notifications\BirthdayInAWeek;
use App\Notifications\BirthdayToday;
use App\Notifications\BirthdayTomorrow;
use App\Services\Birthday\Contracts\DataProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class BirthdayService
{
	private array $syncedUserIds = [];

	public function __construct(private DataProvider $provider)
	{

	}

	public function notifyAboutUpcomingBirthdays()
	{
		$this->notifyAboutBirthday(now(), BirthdayToday::class);
		$this->notifyAboutBirthday(now()->addDay(), BirthdayTomorrow::class);
		$this->notifyAboutBirthday(now()->addWeek(), BirthdayInAWeek::class);
	}

	public function sync()
	{
		$users = collect($this->provider->getUsers());

		foreach ($users as $user) {
			$this->syncedUserIds[] = UserRepository::updateOrCreate($user);
		}

		User::query()->whereNotIn('id', $this->syncedUserIds)->delete();
	}

	private function notifyAboutBirthday(Carbon $targetDate, string $notification)
	{
		$users = UserRepository::fetchByDayMonth($targetDate);

		foreach ($users as $user) {
			$recipients = User::query()
				->whereNotNull('telegram_user_id')
				->whereNot('id', $user->id)
				->get();

			Notification::send($recipients, new $notification($user));
		}
	}
}
