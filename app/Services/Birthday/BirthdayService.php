<?php

namespace App\Services\Birthday;

use App\Models\User;
use App\Services\Birthday\Contracts\DataProvider;

class BirthdayService
{
	private array $existingUserIds = [];

	public function __construct(private DataProvider $provider)
	{

	}

	public function sync()
	{
		$users = collect($this->provider->getUsers());

		foreach ($users as $user) {
			$this->updateOrCreate($user);
		}

		User::query()->whereNotIn('id', $this->existingUserIds)->delete();
	}

	private function updateOrCreate(UserData $user)
	{
		if ($dbUser = $this->fetchByChecksum($user->checksum())) {
			$this->existingUserIds[] = $dbUser->id;

			return;
		}

		$dbUser = $this->fetchByName($user->firstName, $user->lastName);
		$dbUser ? $this->update($dbUser, $user) : $this->create($user);
	}

	private function update(User $dbUser, UserData $userData): void
	{
		$dbUser->fill($userData->toArray());
		$dbUser->checksum = $userData->checksum();
		$dbUser->save();

		$this->existingUserIds[] = $dbUser->id;
	}

	private function create(UserData $userData): void
	{
		$dbUser = new User($userData->toArray());
		$dbUser->checksum = $userData->checksum();
		$dbUser->save();

		$this->existingUserIds[] = $dbUser->id;
	}

	private function fetchByChecksum(string $checksum): ?User
	{
		return User::query()
			->select('id')
			->where('checksum', $checksum)
			->first();
	}

	private function fetchByName(string $firstName, string $lastName): ?User
	{
		return User::query()
			->where('first_name', $firstName)
			->where('last_name', $lastName)
			->first();
	}
}
