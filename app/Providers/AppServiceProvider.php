<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		$this->configureCronRoute();
	}

	protected function configureCronRoute()
	{
		if (!$schedulerToken = env('SCHEDULER_TOKEN')) {
			throw new RuntimeException('SCHEDULER_TOKEN environment variable is not set.');
		}

		Route::get('/schedule-run', function (Request $request) use ($schedulerToken) {
			if (
				($requestToken = $request->input('token')) &&
				($requestToken === $schedulerToken)
			) {
				return Artisan::call('schedule:run');
			}

			abort(404);
		});
	}
}
