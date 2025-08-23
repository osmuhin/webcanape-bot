<?php

namespace App\Providers;

use App\Services\Birthday\Birthday;
use Illuminate\Support\ServiceProvider;

class BirthdayServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(Birthday::class);
	}

	public function boot(Birthday $birthday): void
	{
		$birthday->enableDataProvider(
			'webcanape-yandex-wiki',
			\App\Services\Birthday\DataProviders\WebcanapeYandexWiki\DataProvider::class
		);
	}
}
