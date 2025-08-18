<?php

namespace App\Providers;

use App\Services\Telegram\Telegram;
use App\Services\Telegram\WebhookMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(Telegram::class, function (Application $app) {
			$service = new Telegram($app->get('config')->get('services.telegram'));
			$sdk = $service->getSdk();

			$sdk->addCommands([
				$command
			]);

			return $service;
		});
	}

	public function boot(Telegram $telegram): void
	{
		Route::get($telegram->getWebhookUrl(), function () use ($telegram) {
			$telegram->getSdk()->commandsHandler(webhook: true);
		})->middleware(WebhookMiddleware::class);
	}
}
