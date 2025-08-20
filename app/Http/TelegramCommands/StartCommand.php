<?php

namespace App\Http\TelegramCommands;

use App\Models\TelegramUser;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Chat;

class StartCommand extends Command
{
	protected string $name = 'start';

	public function handle(): void
	{
		$chat = $this->update->getChat();

		$tgUser = TelegramUser::query()
			->where('chat_id', $chat->id)
			->firstOr(fn () => $this->createTgUser($chat));

		if ($tgUser->user()->exists()) {
			if ($tgUser->blocked) {
				$tgUser->blocked = false;
				$tgUser->save();
			}

			return;
		}

		$this->sendFirstGreeting();
	}

	private function createTgUser(Chat $chat): TelegramUser
	{
		$tgUser = new TelegramUser();
		$tgUser->first_name = $chat->first_name;
		$tgUser->last_name = $chat->last_name;
		$tgUser->username = $chat->username;
		$tgUser->chat_id = $chat->id;

		$tgUser->save();

		return $tgUser;
	}

	private function sendFirstGreeting(): void
	{
		$this->replyWithMessage([
			'text' => <<<GREATING
Здарова, ёба\!
Если ты сотрудник webcanape\.ru, то скидывай свое имя и фамилию _\(типа "Иван Иванов"\)_\.
Если испытываешь с этим затруднения, то чекни сотрудников на странице [wiki](https://wiki.yandex.ru/spisok-i-kontaktnye-dannye-sotrudnikov/)\.
GREATING,
			'parse_mode' => 'MarkdownV2'
		]);
	}
}
