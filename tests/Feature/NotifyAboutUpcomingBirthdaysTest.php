<?php

namespace Tests\Feature;

use App\Models\TelegramUser;
use App\Models\User;
use App\Notifications\UpcomingBirthday\InAWeek;
use App\Notifications\UpcomingBirthday\Today;
use App\Notifications\UpcomingBirthday\Tomorrow;
use App\Services\Birthday\Birthday;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotifyAboutUpcomingBirthdaysTest extends TestCase
{
	use RefreshDatabase;

	public static function periodsProvider(): array
	{
		return [
			[Carbon::createFromDate(month: 1, day: 15), Today::class],
			[Carbon::createFromDate(month: 1, day: 15)->subDay(), Tomorrow::class],
			[Carbon::createFromDate(month: 1, day: 15)->subWeek(), InAWeek::class],
		];
	}

	public function setUp(): void
	{
		parent::setUp();

		Notification::fake();
	}

	#[Test]
	public function it_no_notifications_sent_when_no_upcoming_birthdays()
	{
		$service = new Birthday();
		$service->makeNotifier()->notifyAboutUpcomingBirthdays();

		Notification::assertNothingSent();
	}

	#[Test]
	#[DataProvider('periodsProvider')]
	public function it_notifies_about_birthdays(Carbon $travelsTo, string $expectedNotificationClass)
	{
		User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => false]))
			->create([
				'birthdate' => Carbon::createFromDate(month: 1, day: 15)
			]);

		$recipient = User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => false]))
			->create();

		User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => true]))
			->create();

		$this->travelTo($travelsTo);

		$service = new Birthday();
		$service->makeNotifier()->notifyAboutUpcomingBirthdays();

		Notification::assertCount(1);
		Notification::assertSentTo([$recipient->telegramUser], $expectedNotificationClass);
	}
}
