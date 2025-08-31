<?php

namespace App\Services\Telegram;

use App\Models\TelegramUser;
use App\Models\User;
use App\Services\Telegram\Exceptions\TelegramException;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Objects\ChatMemberUpdated;
use Telegram\Bot\Objects\Update;

use function Illuminate\Filesystem\join_paths;

class Telegram
{
	protected TelegramApi $sdk;

	protected Update $update;

	private string $adminChatId;

	private string $webhookUrl;

	private ?string $webhookSecretToken;

	public function __construct(array $config)
	{
		$this->sdk = new TelegramApi($config['bot_token']);
		$this->update = $this->sdk->getWebhookUpdate();

		$this->adminChatId = $config['admin_chat_id'];
		$this->webhookUrl = $config['webhook_url'];
		$this->webhookSecretToken = $config['webhook_secret_token'];
	}

	public function setupWebhook(string $baseUrl)
	{
		$params = ['url' => $this->getWebhookUrl($baseUrl)];

		if ($token = $this->webhookSecretToken) {
			$params['secret_token'] = $token;
		}

		$this->sdk->setWebhook($params);
	}

	public function getWebhookUrl(?string $baseUrl = null): string
	{
		return $baseUrl ? join_paths($baseUrl, $this->webhookUrl) : $this->webhookUrl;
	}

	public function getWebhookSecretToken(): string
	{
		return $this->webhookSecretToken;
	}

	public function getSdk(): TelegramApi
	{
		return $this->sdk;
	}

	public function handleMessageUpdate(Update $update): void
	{
		$handler = new UpdateHandler($update, $this);
		$handler->run();

		if (
			$update->isType('message') &&
			!$update->getMessage()->hasCommand() &&
			$update->getMessage()->isType('text')
		) {
			$this->linkUser(
				$this->getOrCreateTelegramUser($update->getChat()),
				$update
			);

			return;
		}

		if ($update->myChatMember instanceof ChatMemberUpdated) {
			$this->chatMemberUpdated($update->myChatMember);

			return;
		}
	}

	private function chatMemberUpdated(chatMemberUpdated $chatMember): void
	{


			// Log::debug('newChatMember', [$update->myChatMember->newChatMember]);
	}

	public function getOrCreateTelegramUser(Chat $chat): TelegramUser
	{
		return TelegramUser::query()
			->where('chat_id', $chat->id)
			->firstOr(function () use ($chat) {
				$tgUser = new TelegramUser();
				$tgUser->first_name = $chat->first_name;
				$tgUser->last_name = $chat->last_name;
				$tgUser->username = $chat->username;
				$tgUser->chat_id = $chat->id;

				$tgUser->save();

				return $tgUser;
			});
	}

	public function linkUser(TelegramUser $tgUser, Update $update): void
	{
		if ($tgUser->user()->exists()) {
			return;
		}

		$this->findUser($update)->telegramUser()->save($tgUser);

		$this->sdk->sendMessage([
			'chat_id' => $update->getChat()->id,
			'text' => 'Гуд 👍'
		]);
	}

	public function sendMessageToAdmin(string $message, array $options = [])
	{
		return $this->sdk->sendMessage(array_merge([
			'chat_id' => $this->adminChatId,
			'text' => $message
		], $options));
	}

	private function findUser(Update $update): User
	{
		$msgText = $update->getMessage()->text;
		$nameCombination = $this->getFullNameCombination($msgText);

		return User::query()
			->where('name', $nameCombination[0])
			->orWhere('name', $nameCombination[1])
			->firstOr(function () use ($update) {
				throw new TelegramException(
					$update->getChat(),
					"Ты нам незнаком 😢, возможно просто опечатка\.\nСверься с [wiki](https://wiki.yandex.ru/spisok-i-kontaktnye-dannye-sotrudnikov/) компании\)",
					['parse_mode' => 'MarkdownV2']
				);
			});
	}

	private function getFullNameCombination(string $fullName): array
	{
		[$namePart1, $namePart2] = split_full_name($fullName);

		return [
			"{$namePart1} {$namePart2}",
			"{$namePart2} {$namePart1}"
		];
	}
}
