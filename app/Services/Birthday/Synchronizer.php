<?php

namespace App\Services\Birthday;

use App\Models\User;
use App\Services\Birthday\Contracts\DataProvider;
use Illuminate\Support\Collection;

class Synchronizer
{
	private array $syncedIds = [];

	public function __construct(private DataProvider $provider)
	{
		//
	}

	public function sync(): void
	{
		$users = collect($this->provider->getUsers());

		$users = $this->update($users);
		$this->create($users);
		$this->delete();
	}

	/**
	 * @param \Illuminate\Support\Collection<\App\Services\Birthday\UserData> $users
	 *
	 * @return \Illuminate\Support\Collection<\App\Services\Birthday\UserData>
	 */
	private function update(Collection $users): Collection
	{
		$models = User::query()
			->whereIn('name', $users->pluck('name'))
			->get()
			->keyBy('name');

		$unsynced = [];

		/** @var \App\Services\Birthday\UserData $user */
		foreach ($users as $user) {
			/** @var \App\Models\User $model */
			if ($model = $models->get($user->name)) {
				$this->toModel($user, $model)->save();
				$this->syncedIds[] = $model->id;
			} else {
				$unsynced[] = $user;
			}
		}

		return collect($unsynced);
	}

	/**
	 * @param \Illuminate\Support\Collection<\App\Services\Birthday\UserData> $users
	 */
	private function create(Collection $users): void
	{
		$models = $users->map(fn (UserData $siteData) => $this->toModel($siteData));

		User::query()->insert(
			$models->map(fn (User $user) => $user->getAttributes())->toArray()
		);

		$ids = User::query()
			->select('id')
			->whereIn('name', $models->pluck('name'))
			->pluck('id');

		$this->syncedIds = array_merge($this->syncedIds, $ids->toArray());
	}

	private function delete(): void
	{
		User::query()->whereNotIn('id', $this->syncedIds)->delete();
	}

	private function toModel(UserData $userData, ?User $model = null): User
	{
		$model = $model ?? new User();
		$model->fill($userData->toArray());
		$model->birthdate = $userData->birthdate;

		return $model;
	}
}
