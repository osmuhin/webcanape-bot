<?php

namespace App\Services\Telegram;

use Telegram\Bot\Api as TelegramApi;

use function Illuminate\Filesystem\join_paths;

class Telegram
{
	protected TelegramApi $sdk;

	private string $adminChatId;

	private string $webhookUrl;

	private ?string $webhookSecretToken;

	public function __construct(array $config)
	{
		$this->sdk = new TelegramApi($config['bot_token']);
		$this->adminChatId = $config['admin_chat_id'];
		$this->webhookUrl = $config['webhook_url'];
		$this->webhookSecretToken = $config['webhook_secret_token'];
	}

	public function setupWebhook(?string $baseUrl = null)
	{
		$baseUrl ??= config('app.url');

		$params = ['url' => join_paths($baseUrl, $this->webhookUrl)];

		if ($token = $this->webhookSecretToken) {
			$params['secret_token'] = $token;
		}

		$this->sdk->setWebhook($params);
	}

	public function getWebhookUrl(): string
	{
		return $this->webhookUrl;
	}

	public function getWebhookSecretToken(): string
	{
		return $this->webhookSecretToken;
	}
}
