<?php

namespace App\Services\Birthday;

use App\Services\Birthday\Contracts\DataProvider;

class BirthdayService
{
	public function __construct(private DataProvider $provider)
	{

	}

	public function sync()
	{
		$users = $this->provider->getUsers();

		dd($users);
	}
}
