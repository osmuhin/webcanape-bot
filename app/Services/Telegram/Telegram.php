<?php

namespace App\Services\Telegram;

use Telegram\Bot\Api as TelegramApi;

use function Illuminate\Filesystem\join_paths;

class Telegram
{
	protected TelegramApi $sdk;

	private string $adminChatId;

	private string $webhookBaseUrl;

	private string $webhookUrl;

	private ?string $webhookSecretToken;

	public function __construct(array $config)
	{
		$this->sdk = new TelegramApi($config['bot_token']);

		$this->adminChatId =        $config['admin_chat_id'];
		$this->webhookBaseUrl =     $config['webhook_base_url'];
		$this->webhookUrl =         $config['webhook_url'];
		$this->webhookSecretToken = $config['webhook_secret_token'];
	}

	public function setupWebhook()
	{
		$params = ['url' => $this->getWebhookUrl(abs: true)];

		if ($token = $this->webhookSecretToken) {
			$params['secret_token'] = $token;
		}

		$this->sdk->setWebhook($params);
	}

	public function getWebhookUrl(bool $abs = false): string
	{
		return $abs ? join_paths($this->webhookBaseUrl, $this->webhookUrl) : $this->webhookUrl;
	}

	public function getWebhookSecretToken(): string
	{
		return $this->webhookSecretToken;
	}

	public function getSdk(): TelegramApi
	{
		return $this->sdk;
	}
}
