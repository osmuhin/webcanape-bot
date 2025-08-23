<?php

namespace App\Http\TelegramCommands;

use App\Services\Telegram\Telegram;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
	protected string $name = 'start';

	public function handle(): void
	{
		$tgUser = app(Telegram::class)->getOrCreateTelegramUser(
			$this->getUpdate()->getChat()
		);

		if ($tgUser->blocked) {
			$tgUser->blocked = false;
			$tgUser->save();
		}

		if ($tgUser->user()->doesntExist()) {
			$this->sendFirstGreeting();
		}
	}

	private function sendFirstGreeting(): void
	{
		$this->replyWithMessage([
			'text' => $this->getGreetingText(),
			'parse_mode' => 'MarkdownV2'
		]);
	}

	protected function getGreetingText(): string
	{
		return <<<GREATING
Здарова, ёба\!
Если ты сотрудник webcanape\.ru, то скидывай свое имя и фамилию _\(типа "Иван Иванов"\)_\.
Если испытываешь с этим затруднения, то чекни сотрудников на странице [wiki](https://wiki.yandex.ru/spisok-i-kontaktnye-dannye-sotrudnikov/)\.
GREATING;
	}
}
