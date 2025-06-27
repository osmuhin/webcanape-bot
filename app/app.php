<?php

use Illuminate\Foundation\Application;

putenv('APP_SERVICES_CACHE=' . __DIR__ . '/../.runtime/cache/services.php');
putenv('APP_PACKAGES_CACHE=' . __DIR__ . '/../.runtime/cache/packages.php');
putenv('APP_CONFIG_CACHE='   . __DIR__ . '/../.runtime/cache/config.php');
putenv('APP_ROUTES_CACHE='   . __DIR__ . '/../.runtime/cache/routes-v7.php');
putenv('APP_EVENTS_CACHE='   . __DIR__ . '/../.runtime/cache/events.php');

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withProviders([
		App\Providers\AppServiceProvider::class
	])
	->create()
	->useBootstrapPath(__DIR__);
