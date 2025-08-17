<?php

namespace App\Services\Birthday;

use App\Models\TelegramUser;
use App\Models\User;
use App\Notifications\BirthdayInAWeek;
use App\Notifications\BirthdayToday;
use App\Notifications\BirthdayTomorrow;
use App\Services\Birthday\Contracts\DataProvider;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

class BirthdayService
{
	private array $dataProviders = [];

	public function enableDataProvider(string $name, string $dataProviderClass): void
	{
		$this->dataProviders[$name] = $dataProviderClass;
	}

	public function makeSynchronizer(DataProvider|string|null $provider = null): Synchronizer
	{
		return new Synchronizer(
			$this->resolveDataProvider($provider)
		);
	}

	public function makeNotifier(): Notifier
	{
		return new Notifier();
	}

	private function resolveDataProvider(DataProvider|string|null $provider): DataProvider
	{
		if ($provider instanceof DataProvider) {
			return $provider;
		}

		$config = Container::getInstance()->make('config');

		if ($provider === null) {
			$provider = $config->get('birthday.default_data_provider');
		}

		if (!isset($this->dataProviders[$provider])) {
			throw new InvalidArgumentException("Birthday users data provider \"{$provider}\" not found.");
		}

		return $provider::make($config);
	}
}
