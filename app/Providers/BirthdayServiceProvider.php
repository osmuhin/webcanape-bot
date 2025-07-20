<?php

namespace App\Providers;

use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\DataProviders\WebcanapeYandexWiki\DataProvider as WebCanapeYandexWiki;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class BirthdayServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(BirthdayService::class, function (Application $app) {
			$config = $app->get('config');

			return new BirthdayService(
				match ($config->get('birthday.default_data_provider')) {
					'webcanape-yandex-wiki' => $this->getWebCanapeYandexWikiDataProvider($config)
				}
			);
		});
	}

	public function boot(): void
	{
		//
	}

	protected function getWebCanapeYandexWikiDataProvider(Repository $config)
	{
		$provider = new WebCanapeYandexWiki(
			config: $config->get('birthday.webcanape-yandex-wiki')
		);

		$wiki = new YandexWiki(
			token: $config->get('services.yandex.oauth_token'),
			orgId: $config->get('services.yandex.org_id')
		);

		$provider->setYandexWikiClient($wiki);

		return $provider;
	}
}
