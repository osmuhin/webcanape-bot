<?php

namespace Tests\Feature\Telegram;

use App\Services\Telegram\Telegram;
use App\Services\Telegram\WebhookMiddleware;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class TelegramServiceProviderTest extends TestCase
{
	#[Test]
	public function it_register_webhook_route()
	{
		$webhookUrl = trim(config('services.telegram.webhook_url'), '/');

		$route = collect(app('router')->getRoutes())
			->first(fn ($route) =>
				$route->uri() === $webhookUrl &&
				$route->methods()[0] === 'POST'
			);

		$this->assertNotNull($route, "Telegram webhook route {$webhookUrl} is not registered");

		$middlewares = $route->gatherMiddleware();

		$this->assertContains(WebhookMiddleware::class, $middlewares);
	}

	#[Test]
	public function it_triggers_command_handler_on_webhook()
	{
		$this->withoutMiddleware(WebhookMiddleware::class);

		$rawUpdate = json_decode(
			file_get_contents(base_path('tests/Fixtures/telegram-updates/start.json')),
			associative: true
		);
		$update = new Update($rawUpdate);

		$sdk = Mockery::mock(TelegramApi::class, function (MockInterface $mock) use ($update) {
			$mock->shouldReceive('commandsHandler')->once()->andReturn($update);
		});

		$this->mock(Telegram::class, function (MockInterface $mock) use ($sdk, $update) {
			$mock->shouldReceive('getSdk')->once()->andReturn($sdk);
			$mock->shouldReceive('handleMessageUpdate')->with($update)->once();
		});

		$response = $this->postJson('/telegram/webhook', $rawUpdate);
		$response->assertOk();
	}
}
