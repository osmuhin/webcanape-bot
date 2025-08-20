<?php

namespace App\Http\TelegramCommands;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
	protected string $name = 'start';

	public function handle(): void
	{
		$update = $this->update->getChat();

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
