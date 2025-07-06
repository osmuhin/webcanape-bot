<?php

namespace App\Services\Birthday\Contracts;

use Illuminate\Support\Collection;

interface DataProvider
{
	/**
	 * @return \Illuminate\Support\Collection<int, \App\Models\User>
	 */
	public function getUsers(): Collection;
}
