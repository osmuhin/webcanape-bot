<?php

namespace Tests\Fixtures;

use App\Services\Birthday\Contracts\DataProvider as DataProviderContract;
use App\Services\Birthday\UserData;

class BirthdayDataProvider implements DataProviderContract
{
	public function getUsers(): array
	{
		return [
			$this->makeIvanov(),
			$this->makePetrov()
		];
	}

	private function makeIvanov(): UserData
	{
		$ivanov = new UserData();
		$ivanov->firstName = 'Иван';
		$ivanov->lastName = 'Иванов';
		$ivanov->birthdate = now()->setDate(2025, 5, 20);
		$ivanov->photo = '/storage/ivanov.png';
		$ivanov->post = 'Директор';

		return $ivanov;
	}

	private function makePetrov(): UserData
	{
		$petrov = new UserData();
		$petrov->firstName = 'Арсений';
		$petrov->lastName = 'Петров';
		$petrov->birthdate = now()->setDate(2024, 9, 30);
		$petrov->photo = '/storage/petrov.png';
		$petrov->post = 'Дизайнер';

		return $petrov;
	}
}
