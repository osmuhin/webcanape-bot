<?php

namespace App\Services\Birthday\Contracts;

use Illuminate\Support\Collection;

interface DataProvider
{
	/**
	 * @return \App\Services\Birthday\UserData[]
	 */
	public function getUsers(): array;
}
