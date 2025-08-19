<?php

namespace Tests\Feature\Telegram;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Telegram\Bot\Api as TelegramApi;
use Tests\TestCase;

class StartCommandTest extends TestCase
{
	use RefreshDatabase;

	public function test_example(): void
	{
		$tg = new TelegramApi();
		$request = Mockery::mock(RequestInterface::class, function (MockInterface $mock) {
			$stream = Mockery::mock(StreamInterface::class, function (MockInterface $mock) {
				$mock->shouldReceive('__toString')->andReturn(
					file_get_contents(base_path('tests/Fixtures/telegram-updates/start.json'))
				);
			});

			$mock->shouldReceive('getBody')->andReturn($stream);
		});

		$update = $tg->commandsHandler(webhook: true, request: $request);

		dd($update->getMessage());
	}
}
