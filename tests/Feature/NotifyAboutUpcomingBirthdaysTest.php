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
			->has(TelegramUser::factory())
			->create([
				'birthdate' => Carbon::createFromDate(month: 1, day: 15)
			]);

		$recipient = User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => false]))
			->create();

		$this->travelTo($travelsTo);

		$service = new Birthday();
		$service->makeNotifier()->notifyAboutUpcomingBirthdays();

		Notification::assertSentTo([$recipient->telegramUser], $expectedNotificationClass);
	}

	#[Test]
	public function it_does_not_send_notifications_if_recipient_blocked_the_bot()
	{
		$date = Carbon::createFromDate(month: 1, day: 15);

		$this->travelTo($date);

		User::factory()
			->has(TelegramUser::factory())
			->create(['birthdate' => $date]);

		User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => true]))
			->create();

		$service = new Birthday();
		$service->makeNotifier()->notifyAboutUpcomingBirthdays();

		Notification::assertNothingSent();
	}

	#[Test]
	public function it_does_not_send_notifications_if_birthday_person_hides_from_others()
	{
		$date = Carbon::createFromDate(month: 1, day: 15);

		$this->travelTo($date);

		User::factory()
			->has(TelegramUser::factory())
			->create([
				'birthdate' => $date,
				'hidden_from_other' => true
			]);

		User::factory()
			->has(TelegramUser::factory()->state(fn () => ['blocked' => false]))
			->create();

		$service = new Birthday();
		$service->makeNotifier()->notifyAboutUpcomingBirthdays();

		Notification::assertNothingSent();
	}
}
