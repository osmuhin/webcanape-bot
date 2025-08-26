<?php

namespace App\Services\Birthday;

use Carbon\Carbon;

class UserData
{
	public ?string $name;

	public ?Carbon $birthdate;

	public ?string $photo;

	public ?string $post;

	public bool $notifyAboutBirthday = true;

	public function toArray()
	{
		return [
			'name' => $this->name,
			'birthdate' => $this->birthdate->toDateString(),
			'photo' => $this->photo,
			'post' => $this->post,
			'notify_about_birthday' => $this->notifyAboutBirthday,
		];
	}
}
