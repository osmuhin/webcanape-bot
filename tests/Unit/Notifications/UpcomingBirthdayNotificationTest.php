<?php

namespace Tests\Unit\Notifications;

use App\Models\TelegramUser;
use App\Models\User;
use App\Notifications\UpcomingBirthday\InAWeek;
use App\Notifications\UpcomingBirthday\Today;
use App\Notifications\UpcomingBirthday\Tomorrow;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class UpcomingBirthdayNotificationTest extends TestCase
{
	public static function messagesProvider(): array
    {
        return [
            [Today::class, <<<MSG
🎉🎁 Иван Иванов (Программист) <b><u>сегодня</u></b> празднует день рождения (15 января).<br>
<a href="/image.png">Фото</a>
MSG],
            [Tomorrow::class,
<<<MSG
🟠 Иван Иванов (Программист) <b><u>завтра</u></b> будет праздновать день рождения (15 января).<br>
<a href="/image.png">Фото</a>
MSG],
            [InAWeek::class,
<<<MSG
🟢 Иван Иванов (Программист) <b><u>через неделю</u></b> (15 января) будет праздновать день рождения.<br>
<a href="/image.png">Фото</a>
MSG]
        ];
    }

	#[Before]
	public function setLocale()
	{
		Carbon::setLocale('ru');
	}

	#[Test]
	#[DataProvider('messagesProvider')]
	public function it_makes_telegram_message(string $notificationClass, string $expectedMessage): void
	{
		$bDayPerson = Mockery::mock(User::class);
		$bDayPerson->allows('getAttribute')->with('name')->andReturn('Иван Иванов');
		$bDayPerson->allows('getAttribute')->with('birthdate')->andReturn(Carbon::createFromDate(month: 1, day: 15));
		$bDayPerson->allows('getAttribute')->with('post')->andReturn('Программист');
		$bDayPerson->allows('getAttribute')->with('photo')->andReturn('/image.png');

		$recipient1 = Mockery::mock(TelegramUser::class);
		$recipient1->allows('getAttribute')->with('chat_id')->andReturn(14);
		$recipient1->allows('getAttribute')->with('blocked')->andReturn(false);

		$recipient2 = Mockery::mock(TelegramUser::class);
		$recipient2->allows('getAttribute')->with('chat_id')->andReturn(12);
		$recipient2->allows('getAttribute')->with('blocked')->andReturn(true);

		$notification = new $notificationClass($bDayPerson);
		$message1 = $notification->toTelegram($recipient1);
		$message2 = $notification->toTelegram($recipient2);

		assertTrue($message1->canSend());
		assertFalse($message2->canSend());

		assertSame(
			$expectedMessage,
			$message1->getPayloadValue('text')
		);

		assertSame(
			14,
			$message1->getPayloadValue('chat_id')
		);
	}
}
