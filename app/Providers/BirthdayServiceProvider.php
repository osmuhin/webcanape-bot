<?php

namespace App\Providers;

use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\DataProviders\WebcanapeYandexWiki\DataProvider as WebCanapeYandexWiki;
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
					'webcanape-yandex-wiki' => $this->getWebCanapeYandexWikiDataProvider()
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

	protected function getWebCanapeYandexWikiDataProvider()
	{
		$provider = new WebCanapeYandexWiki(
			config: config('birthday.webcanape-yandex-wiki')
		);

		$wiki = new YandexWiki(
			token: config('services.yandex.oauth_token'),
			orgId: config('services.yandex.org_id')
		);

		$provider->setYandexWikiClient($wiki);

		return $provider;
	}
}
