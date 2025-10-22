<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->extendFaker();
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
