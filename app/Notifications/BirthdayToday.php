<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\Enums\ParseMode;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class BirthdayToday extends Notification implements ShouldQueue
{
	use Queueable;

	public function __construct(private User $bdayPerson)
	{
		//
	}

	public function via(object $notifiable): array
	{
		return ['telegram'];
	}

	public function toTelegram(User $notifiable)
	{
		// if ($this->bdayPerson->photo) {
			// $message = TelegramFile::create();
			// $message->photo($this->bdayPerson->photo);
		// } else {
			$message = TelegramMessage::create();
		// }

		$date = Carbon::parse($this->bdayPerson->birthdate)->translatedFormat('d F');

		$message->sendWhen((bool) $notifiable->telegram_user_id)
			->parseMode(ParseMode::HTML)
			->to($notifiable->telegram_user_id)
			->content("🎉🎁 {$this->bdayPerson->first_name} {$this->bdayPerson->last_name} ({$this->bdayPerson->post}) <b><u>сегодня</u></b> празднует день рождения ({$date}).");

		return $message;
	}
}
