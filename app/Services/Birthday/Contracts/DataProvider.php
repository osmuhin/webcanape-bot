<?php

namespace App\Services\Birthday\Contracts;

use Illuminate\Contracts\Config\Repository;

interface DataProvider
{
	public static function make(Repository $config): self;

	/**
	 * @return \App\Services\Birthday\UserData[]
	 */
	public function getUsers(): array;
}
