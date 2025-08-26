<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->configureCronRoute();
	}

	/**
	 * @throws \RuntimeException
	 */
	protected function configureCronRoute()
	{
		if (!$schedulerToken = config('app.scheduler_token')) {
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
