<?php

namespace App\Services\Birthday;

use App\Models\TelegramUser;
use App\Models\User;
use App\Notifications\UpcomingBirthday\InAWeek;
use App\Notifications\UpcomingBirthday\Today;
use App\Notifications\UpcomingBirthday\Tomorrow;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class Notifier
{
	public function __construct()
	{
		//
	}

	public function notifyAboutUpcomingBirthdays(): void
	{
		$this->notifyAboutBirthday(now(), Today::class);
		$this->notifyAboutBirthday(now()->addDay(), Tomorrow::class);
		$this->notifyAboutBirthday(now()->addWeek(), InAWeek::class);
	}

	private function notifyAboutBirthday(Carbon $targetDate, string $notificationClass): void
	{
		$bDayPersons = $this->getBirthdayPersons($targetDate);

		foreach ($bDayPersons as $bDayPerson) {
			$recipients = $this->getRecipients($bDayPerson);

			Notification::send($recipients, new $notificationClass($bDayPerson));
		}
	}

	private function getBirthdayPersons(Carbon $targetDate): Collection
	{
		return User::query()
			->whereRaw("DATE_FORMAT(birthdate, '%d-%m') = ?", [$targetDate->format('d-m')])
			->get();
	}

	private function getRecipients(User $bDayPerson): Collection
	{
		return TelegramUser::query()
			->whereHas(
				'user',
				fn (Builder $query) => $query->whereNot('id', $bDayPerson->id)
			)
			->where('blocked', false)
			->get();
	}
}
