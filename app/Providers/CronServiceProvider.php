<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class CronServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		if (!$schedulerToken = config('app.scheduler_token')) {
			throw new RuntimeException('SCHEDULER_TOKEN environment variable is not set.');
		}

		Route::get('/schedule-run', function (Request $request) use ($schedulerToken) {
			if (
				($requestToken = $request->input('token')) &&
				($requestToken === $schedulerToken)
			) {
				Artisan::call('schedule:run');

				return response()->noContent();
			}

			abort(404);
		});
	}
}
