<?php

namespace App\Services\Birthday;

use Carbon\Carbon;

class UserData
{
	public ?string $name;

	public ?Carbon $birthdate;

	public ?string $photo;

	public ?string $post;

	public bool $hiddenFromOther = false;

	public function toArray()
	{
		return [
			'name' => $this->name,
			'birthdate' => $this->birthdate->toDateString(),
			'photo' => $this->photo,
			'post' => $this->post,
			'hidden_from_other' => $this->hiddenFromOther,
		];
	}
}
