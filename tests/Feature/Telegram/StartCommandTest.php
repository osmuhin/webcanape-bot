<?php

namespace Tests\Feature\Telegram;

use App\Http\TelegramCommands\StartCommand;
use App\Models\TelegramUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

use function PHPUnit\Framework\assertSame;

class StartCommandTest extends TestCase
{
	use RefreshDatabase;

	private Update $update;

	private Chat $chat;

	public function setUp(): void
	{
		parent::setUp();

		$this->update = new Update(json_decode(
			file_get_contents(base_path('tests/Fixtures/telegram-updates/start.json')),
			associative: true
		));

		$this->chat = $this->update->getChat();
	}

	#[Test]
	public function it_has_proper_command_name(): void
	{
		$command = new StartCommand();

		assertSame('start', $command->getName());
	}

	#[Test]
	public function it_creates_telegram_user_while_handling_start_command(): void
	{
		$command = new StartCommand();

		$sdk = $this->makeSdkExpectSendingMessage();

		$command->make($sdk, $this->update, []);

		$this->assertDatabaseHas('telegram_users', [
			'first_name' => $this->chat->first_name,
			'last_name' => $this->chat->last_name,
			'username' => $this->chat->username,
			'chat_id' => $this->chat->id,
			'user_id' => null,
			'blocked' => false
		]);
	}

	#[Test]
	public function it_unblock_telegram_user_if_one_exists_while_handling_start_command(): void
	{
		TelegramUser::factory()->create(['chat_id' => $this->chat->id, 'blocked' => true, 'user_id' => null]);

		$command = new StartCommand();

		$sdk = $this->makeSdkExpectSendingMessage();

		$command->make($sdk, $this->update, []);

		$this->assertDatabaseHas('telegram_users', [
			'chat_id' => $this->update->getChat()->id,
			'blocked' => false
		]);
	}

	#[Test]
	public function it_does_not_send_message_if_telegram_user_already_bound_to_user(): void
	{
		TelegramUser::factory()->create(['chat_id' => $this->update->getChat()->id]);

		$command = new StartCommand();
		$sdk = Mockery::spy(TelegramApi::class);
		$command->make($sdk, $this->update, []);
		$sdk->shouldNotHaveReceived('sendMessage');
	}

	/**
	 * @return \Mockery\LegacyMockInterface&\Mockery\MockInterface&\Telegram\Bot\Api
	 */
	private function makeSdkExpectSendingMessage(): TelegramApi
	{
		$mock = Mockery::mock(TelegramApi::class);
		$mock->shouldReceive('sendMessage')
			->with(Mockery::on(fn ($args) =>
				$args['chat_id'] == $this->chat->id
				&& $args['text']
				&& $args['parse_mode'] === 'MarkdownV2'
			))
			->once();

		return $mock;
	}
}
