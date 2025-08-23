<?php

namespace App\Services\Birthday;

use App\Services\Birthday\Contracts\DataProvider;
use Illuminate\Container\Container;
use InvalidArgumentException;

class Birthday
{
	private array $dataProviders = [];

	public function enableDataProvider(string $name, DataProvider|string $dataProviderClass): void
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

	/**
	 * @throws \InvalidArgumentException
	 */
	public function resolveDataProvider(DataProvider|string|null $provider): DataProvider
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

		$provider = $this->dataProviders[$provider];

		if ($provider instanceof DataProvider) {
			return $provider;
		}

		return $provider::make($config);
	}
}
