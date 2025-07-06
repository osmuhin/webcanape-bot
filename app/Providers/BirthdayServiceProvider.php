<?php

namespace App\Providers;

use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\DataProviders\WebcanapeYandexWiki\WebcanapeYandexWikiDataProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class BirthdayServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		$this->app->singleton(BirthdayService::class, function (Application $app) {
			return new BirthdayService(
				match (config('birthday.default_data_provider')) {
					'webcanape-yandex-wiki' => new WebcanapeYandexWikiDataProvider(
						config: config('birthday.webcanape-yandex-wiki')
					)
				}
			);
		});
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		//
	}
}
