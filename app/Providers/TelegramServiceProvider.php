<?php

namespace App\Providers;

use App\Http\TelegramCommands\StartCommand;
use App\Services\Telegram\Telegram;
use App\Services\Telegram\WebhookMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

class TelegramServiceProvider extends ServiceProvider
{
	public const ROUTE_NAME = 'telegram-webhook';

	public function register(): void
	{
		$this->app->singleton(Telegram::class, function (Application $app) {
			$service = new Telegram($app->get('config')->get('services.telegram'));

			$service->getSdk()->addCommands([
				StartCommand::class
			]);

			return $service;
		});
	}

	public function boot(): void
	{
		$this->configureRoute();

		$handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

		if ($handler instanceof Handler) {
			$this->configureExceptionsHanling($handler);
		}
	}

	protected function configureRoute(): void
	{
		Route::post(app(Telegram::class)->getWebhookUrl(), function () {
			$telegram = app(Telegram::class);
			$update = $telegram->getSdk()->commandsHandler(webhook: true);

			Log::debug('tg-webhook: ', request()->all());

			$telegram->handleMessageUpdate($update);
		})
			->name(self::ROUTE_NAME)
			->middleware(WebhookMiddleware::class);
	}

	protected function configureExceptionsHanling(Handler $handler)
	{
		$handler->reportable(function (Throwable $e) {
			if ($e instanceof TelegramSDKException) {
				return true;
			}

			app(Telegram::class)->sendMessageToAdmin(
				"Exception \"{$e->getMessage()}\" at {$e->getFile()}:{$e->getLine()}"
			);
		});

		$handler->renderable(function (Throwable $e, Request $request) {
			if ($request->routeIs(TelegramServiceProvider::ROUTE_NAME)) {
				return new Response();
			}

			return null;
		});
	}
}
