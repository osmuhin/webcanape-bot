<?php

namespace App\Providers;

use App\Http\TelegramCommands\StartCommand;
use App\Services\Telegram\Telegram;
use App\Services\Telegram\WebhookMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
		$this->configureExceptionsHanling();
	}

	protected function configureRoute(): void
	{
		Route::post(app(Telegram::class)->getWebhookUrl(), function () {
			$telegram = app(Telegram::class);
			$update = $telegram->getSdk()->commandsHandler(webhook: true);

			// Log::debug('Tg message: ', request()->all());

			// Обработает сообщение, только если оно не является командой
			if (
				!$update->getMessage()->hasCommand() &&
				$update->isType('message') &&
				$update->getMessage()->isType('text')
			) {
				$telegram->handleMessageUpdate($update);
			}
		})
			->name(self::ROUTE_NAME)
			->middleware(WebhookMiddleware::class);
	}

	protected function configureExceptionsHanling()
	{
		/** @var \Illuminate\Foundation\Exceptions\Handler $handler */
		$handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

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
