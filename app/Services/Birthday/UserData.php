<?php

namespace App\Services\Birthday;

use Carbon\Carbon;

class UserData
{
	public ?string $firstName;

	public ?string $lastName;

	public ?Carbon $birthdate;

	public ?string $photo;

	public ?string $post;

	public function checksum()
	{
		return md5(
			json_encode($this->toArray(), JSON_THROW_ON_ERROR)
		);
	}

	public function toArray()
	{
		return [
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'birthdate' => $this->birthdate->toDateString(),
			'photo' => $this->photo,
			'post' => $this->post,
		];
	}
}
