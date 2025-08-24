<?php

namespace App\Services\Telegram\Exceptions;

use App\Services\Telegram\Telegram;
use Exception;
use Illuminate\Http\Response;
use Telegram\Bot\Objects\Chat;

class TelegramException extends Exception
{
	public function __construct(
		protected Chat $chat,
		string $message,
		protected array $sendMessageOptions = []
	)
	{
		parent::__construct($message);
	}

	public function report(): void
	{
		app(Telegram::class)->getSdk()->sendMessage(
			array_merge([
				'chat_id' => $this->chat->id,
				'text' => $this->message
			], $this->sendMessageOptions)
		);
	}

	public function render()
	{
		return new Response();
	}
}
