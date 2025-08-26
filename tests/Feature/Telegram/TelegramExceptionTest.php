<?php

namespace Tests\Feature\Telegram;

use App\Services\Telegram\Exceptions\TelegramException;
use App\Services\Telegram\Telegram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class TelegramExceptionTest extends TestCase
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
	public function it_dispatches_job_and_sends_message_when_exception_reported()
	{
		$exception = new TelegramException(
			$this->update->getChat(),
			'something wrong',
			['some-option' => 2, 'chat_id' => '42']
		);

		$this->mock(Telegram::class, function (MockInterface $mock) {
			$mock->shouldReceive('getSdk')->once()->andReturn(
				Mockery::mock(TelegramApi::class, function (MockInterface $mock) {
					$mock->shouldReceive('sendMessage')->once()->with(
						[
							'chat_id' => '42',
							'text' => 'something wrong',
							'some-option' => 2
						],
					);
				})
			);
		});

		$exception->report();
	}
}
