<?php

namespace App\Providers;

use App\Services\Birthday\BirthdayService;
use Illuminate\Support\ServiceProvider;

class BirthdayServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(BirthdayService::class, function () {
			$service = new BirthdayService();

			$service->enableDataProvider(
				'webcanape-yandex-wiki',
				\App\Services\Birthday\DataProviders\WebcanapeYandexWiki\DataProvider::class
			);
		});
	}
}
