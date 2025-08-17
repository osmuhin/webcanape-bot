<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

putenv('APP_SERVICES_CACHE=' . __DIR__ . '/../.runtime/cache/app/services.php');
putenv('APP_PACKAGES_CACHE=' . __DIR__ . '/../.runtime/cache/app/packages.php');
putenv('APP_CONFIG_CACHE='   . __DIR__ . '/../.runtime/cache/app/config.php');
putenv('APP_ROUTES_CACHE='   . __DIR__ . '/../.runtime/cache/app/routes-v7.php');
putenv('APP_EVENTS_CACHE='   . __DIR__ . '/../.runtime/cache/app/events.php');

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		commands: __DIR__ . '/console.php',
		health: '/up',
	)
	->withProviders([
		\App\Providers\AppServiceProvider::class,
		\App\Providers\BirthdayServiceProvider::class,
		\App\Providers\TelegramServiceProvider::class
	])
	->withExceptions(function (Exceptions $exceptions) {
		//
	})
	->withMiddleware(function (Middleware $middleware) {
		//
	})
	->create()
	->useBootstrapPath(__DIR__);
