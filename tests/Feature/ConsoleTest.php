<?php

namespace Tests\Feature;

use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\Notifier;
use App\Services\Birthday\Synchronizer;
use App\Services\Telegram\Telegram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsoleTest extends TestCase
{
	use RefreshDatabase;

	#[Test]
	public function it_calls_birthdays_synchronization_command(): void
	{
		Queue::fake();

		$this->instance(
			BirthdayService::class,
			Mockery::mock(BirthdayService::class, function (MockInterface $mock) {
				$syncronizer = Mockery::mock(Synchronizer::class, function (MockInterface $mock) {
					$mock->expects('sync')->once();
				});

				$mock->expects('makeSynchronizer')->once()->andReturn($syncronizer);
			})
		);

		Artisan::call('sync-birthdays');

		Queue::assertClosurePushed(function (CallQueuedClosure $job) {
			$job->handle($this->app);

			return true;
		});
	}

	#[Test]
	public function it_calls_birthdays_notify_command(): void
	{
		$this->instance(
			BirthdayService::class,
			Mockery::mock(BirthdayService::class, function (MockInterface $mock) {
				$notifier = Mockery::mock(Notifier::class, function (MockInterface $mock) {
					$mock->expects('notifyAboutUpcomingBirthdays')->once();
				});

				$mock->expects('makeNotifier')->once()->andReturn($notifier);
			})
		);

		Artisan::call('notify');
	}

	#[Test]
	public function it_calls_setup_telegram_webhook_command(): void
	{
		$this->instance(
			Telegram::class,
			Mockery::mock(Telegram::class, function (MockInterface $mock) {
				$mock->expects('setupWebhook')->once();
			})
		);

		Artisan::call('setup-telegram-webhook');
	}
}
