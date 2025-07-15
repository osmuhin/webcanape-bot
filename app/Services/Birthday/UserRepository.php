<?php

namespace App\Services\Birthday;

use App\Models\User;
use Carbon\Carbon;

class UserRepository
{
	public static function fetchByOneWeekBeforeBirthday()
	{
		return self::fetchByDayMonth(
			now()->addWeek(7)
		);
	}

	public static function fetchByOneDayBeforeBirthday()
	{
		return self::fetchByDayMonth(
			now()->addDay()
		);
	}

	public static function fetchByBirthdayToday()
	{
		return self::fetchByDayMonth(
			now()
		);
	}

	public static function fetchByChecksum(string $checksum): ?User
	{
		return User::query()
			->select('id')
			->where('checksum', $checksum)
			->first();
	}

	public static function fetchByName(string $firstName, string $lastName): ?User
	{
		return User::query()
			->where('first_name', $firstName)
			->where('last_name', $lastName)
			->first();
	}

	public static function updateOrCreate(UserData $user): int
	{
		if ($dbUser = self::fetchByChecksum($user->checksum())) {

			return $dbUser->id;
		}

		$dbUser = self::fetchByName($user->firstName, $user->lastName);
		return $dbUser ? self::update($dbUser, $user) : self::create($user);
	}

	public static function update(User $dbUser, UserData $userData): int
	{
		$dbUser->fill($userData->toArray());
		$dbUser->checksum = $userData->checksum();
		$dbUser->save();

		return $dbUser->id;
	}

	public static function create(UserData $userData): int
	{
		$dbUser = new User($userData->toArray());
		$dbUser->checksum = $userData->checksum();
		$dbUser->save();

		return $dbUser->id;
	}

	public static function fetchByDayMonth(Carbon $date)
	{
		return User::query()
			->whereRaw("DATE_FORMAT(birthdate, '%m-%d') = ?", [$date->format('m-d')])
			->get();
	}
}
