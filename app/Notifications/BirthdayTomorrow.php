<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class BirthdayTomorrow extends Notification implements ShouldQueue
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
		$message = TelegramMessage::create();

		$date = Carbon::parse(
			$this->bdayPerson->birthdate
		)->translatedFormat('d F');

		$message->sendWhen((bool) $notifiable->telegram_user_id)
			->to($notifiable->telegram_user_id)
			->content("🟠 {$this->bdayPerson->first_name} {$this->bdayPerson->last_name} ({$this->bdayPerson->post}) *завтра* ({$date}) будет праздновать день рождения.");

		return $message;
	}
}
