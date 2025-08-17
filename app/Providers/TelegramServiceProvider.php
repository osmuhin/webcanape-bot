<?php

namespace App\Providers;

use App\Services\Telegram\Telegram;
use App\Services\Telegram\WebhookMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(Telegram::class, function (Application $app) {
			return new Telegram($app->get('config')->get('services.telegram'));
		});
	}

	public function boot(Telegram $telegram): void
	{
		Route::get($telegram->getWebhookUrl(), function (Request $request) {

		})->middleware(WebhookMiddleware::class);
	}
}
