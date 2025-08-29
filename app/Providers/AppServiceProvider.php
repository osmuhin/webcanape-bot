<?php

namespace App\Providers;

use Carbon\Carbon;
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
		$this->extendFaker();
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

	/**
	 * Расширяет faker методом timestamp, примеры:
	 *
	 * fake()->timestamp()
	 * fake()->timestamp(now()->subWeek(), now())
	 */
	protected function extendFaker(): void
	{
		$faker = fake();
		$faker->addProvider(
			new class($faker) {
				public function timestamp(?Carbon $start = null, ?Carbon $end = null): Carbon
				{
					$start = $start ?? Carbon::now()->subYears(20);
					$end = $end ?? Carbon::now();

					return Carbon::createFromTimestamp(
						mt_rand($start->timestamp, $end->timestamp)
					);
				}
			}
		);
	}
}
