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
			$this->makePetrov(),
			$this->makeSidorov()
		];
	}

	public function makeIvanov(): UserData
	{
		$ivanov = new UserData();
		$ivanov->firstName = 'Иван';
		$ivanov->lastName = 'Иванов';
		$ivanov->birthdate = now()->setDate(2025, 5, 20);
		$ivanov->photo = '/storage/ivanov.png';
		$ivanov->post = 'Директор';

		return $ivanov;
	}

	public function makePetrov(): UserData
	{
		$petrov = new UserData();
		$petrov->firstName = 'Арсений';
		$petrov->lastName = 'Петров';
		$petrov->birthdate = now()->setDate(2024, 9, 30);
		$petrov->photo = '/storage/petrov.png';
		$petrov->post = 'Дизайнер';

		return $petrov;
	}

	public function makeSidorov(): UserData
	{
		$petrov = new UserData();
		$petrov->firstName = 'Михаил';
		$petrov->lastName = 'Сидоров';
		$petrov->birthdate = now()->setDate(2024, 12, 31);
		$petrov->photo = '/storage/sidorov.png';
		$petrov->post = 'Уборщик';

		return $petrov;
	}
}
