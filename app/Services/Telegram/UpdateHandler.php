<?php

namespace App\Services\Telegram;

use Telegram\Bot\Objects\ChatMemberUpdated;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class UpdateHandler
{
	public function __construct(
		protected Update $update,
		protected Telegram $telegram
	)
	{
		//
	}

	public function run(): void
	{
		if ($this->update->message instanceof Message) {
			$this->handleMessage();

			return;
		}

		if ($this->update->myChatMember instanceof ChatMemberUpdated) {

			return;
		}
	}

	protected function handleMessage(): void
	{

	}
}
