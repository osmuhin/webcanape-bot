<?php

namespace App\Notifications\UpcomingBirthday;

use App\Models\TelegramUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\Enums\ParseMode;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class Today extends Notification implements ShouldQueue
{
	use Queueable;

	public function __construct(private User $bdayPerson)
	{
		//
	}

	public function via(): array
	{
		return ['telegram'];
	}

	public function toTelegram(TelegramUser $recipient): TelegramMessage
	{
		// if ($this->bdayPerson->photo) {
			// $message = TelegramFile::create();
			// $message->photo($this->bdayPerson->photo);
		// } else {
			$message = TelegramMessage::create();
		// }

		$date = Carbon::parse($this->bdayPerson->birthdate)->translatedFormat('d F');

		$message->sendWhen(!$recipient->blocked)
			->parseMode(ParseMode::HTML)
			->to($recipient->chat_id)
			->content("🎉🎁 {$this->bdayPerson->name} ({$this->bdayPerson->post}) <b><u>сегодня</u></b> празднует день рождения ({$date}).");

		return $message;
	}
}
