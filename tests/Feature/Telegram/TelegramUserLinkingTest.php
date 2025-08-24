<?php

namespace Tests\Feature\Telegram;

use App\Models\TelegramUser;
use App\Models\User;
use App\Services\Telegram\Exceptions\TelegramException;
use App\Services\Telegram\Telegram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class TelegramUserLinkingTest extends TestCase
{
	use RefreshDatabase;

	private Update $update;

	public function setUp(): void
	{
		parent::setUp();

		$this->update = new Update(json_decode(
			file_get_contents(base_path('tests/Fixtures/telegram-updates/my-name.json')),
			associative: true
		));
	}

	#[Test]
	public function it_throws_exception_when_user_not_found(): void
	{
		$tgUser = TelegramUser::factory()->create(['user_id' => null]);

		$tg = app(Telegram::class);

		$this->expectException(TelegramException::class);
		$this->expectExceptionMessageMatches("/Ты нам незнаком/");

		$tg->linkUser($tgUser, $this->update);
	}

	#[Test]
	public function it_links_telegram_user_to_existing_user_and_dispatches_answer(): void
	{
		$tgUser = TelegramUser::factory()->create(['user_id' => null]);
		$user = User::factory()->create(['name' => 'Иванов Иван']);

		$tg = $this->createPartialMock(Telegram::class, ['getSdk']);
		$tg->method('getSdk')->willReturn(
			Mockery::mock(TelegramApi::class, function (MockInterface $mock) {
				$mock->shouldReceive('sendMessage')
					->once()
					->with([
						'chat_id' => $this->update->getChat()->id,
						'text' => 'Гуд 👍'
					]);
			})
		);

		$tg->linkUser($tgUser, $this->update);

		$this->assertDatabaseHas('telegram_users', [
			'id' => $tgUser->id,
			'user_id' => $user->id
		]);
	}
}
