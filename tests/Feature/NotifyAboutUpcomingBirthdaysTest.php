<?php

namespace Tests\Feature;

use App\Notifications\BirthdayInAWeek;
use App\Notifications\BirthdayToday;
use App\Notifications\BirthdayTomorrow;
use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\UserRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Fixtures\BirthdayDataProvider;
use Tests\TestCase;

use function PHPUnit\Framework\assertSame;

class NotifyAboutUpcomingBirthdaysTest extends TestCase
{
	use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();

		Notification::fake();
	}

	public function test_no_notifications_sent_when_no_upcoming_birthdays()
	{
		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();
		$service->notifyAboutUpcomingBirthdays();

		Notification::assertNothingSent();
	}

	public function test_notify_about_todays_birthdays()
	{
		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();

		$ivanov = UserRepository::fetchByName('Иван', 'Иванов');
		$ivanov->telegram_user_id = '1';
		$ivanov->save();

		$petrov = UserRepository::fetchByName('Арсений', 'Петров');
		$petrov->telegram_user_id = '2';
		$petrov->save();

		$sidorov = UserRepository::fetchByName('Михаил', 'Сидоров');
		$sidorov->telegram_user_id = '3';
		$sidorov->telegram_allow_notifications = false;
		$sidorov->save();

		// Ivanov's birthdate
		$this->travelTo(Carbon::createFromDate(month: 5, day: 20));

		$service->notifyAboutUpcomingBirthdays();

		Notification::assertCount(1);
		Notification::assertSentTo(
			[$petrov],
			function (BirthdayToday $notification) use ($petrov) {
				$message = $notification->toTelegram($petrov)->toArray();
				$text = $message['text'];
				$chatId = $message['chat_id'];

				assertSame('🎉🎁 Иван Иванов (Директор) <b><u>сегодня</u></b> празднует день рождения (20 мая).', $text);
				assertSame('2', $chatId);

				return true;
			}
		);
	}

	public function test_notify_about_tomorrow_birthdays()
	{
		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();

		$petrov = UserRepository::fetchByName('Арсений', 'Петров');
		$petrov->telegram_user_id = '2';
		$petrov->save();

		// The day before Ivanov's birthdate
		$this->travelTo(Carbon::createFromDate(month: 5, day: 20)->subDay());

		$service->notifyAboutUpcomingBirthdays();

		Notification::assertCount(1);
		Notification::assertSentTo(
			[$petrov],
			function (BirthdayTomorrow $notification) use ($petrov) {
				$message = $notification->toTelegram($petrov)->toArray();
				$text = $message['text'];

				assertSame('🟠 Иван Иванов (Директор) <b><u>завтра</u></b> будет праздновать день рождения (20 мая).', $text);

				return true;
			}
		);
	}

	public function test_notify_about_birthdays_the_week_before()
	{
		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();

		$petrov = UserRepository::fetchByName('Арсений', 'Петров');
		$petrov->telegram_user_id = '2';
		$petrov->save();

		// The week before Ivanov's birthdate
		$this->travelTo(Carbon::createFromDate(month: 5, day: 20)->subWeek());

		$service->notifyAboutUpcomingBirthdays();

		Notification::assertCount(1);
		Notification::assertSentTo(
			[$petrov],
			function (BirthdayInAWeek $notification) use ($petrov) {
				$message = $notification->toTelegram($petrov)->toArray();
				$text = $message['text'];

				assertSame('🟢 Иван Иванов (Директор) <b><u>через неделю</u></b> (20 мая) будет праздновать день рождения.', $text);

				return true;
			}
		);
	}
}
