<?php

namespace Tests\Fixtures;

use App\Services\Birthday\Contracts\DataProvider as DataProviderContract;
use App\Services\Birthday\UserData;
use Illuminate\Contracts\Config\Repository;

class BirthdayDataProvider implements DataProviderContract
{
	public static function make(Repository $_): self
	{
		return new self();
	}

	public function getUsers(): array
	{
		return [
			$this->makeIvanov(),
			$this->makePetrov(),
			$this->makeSidorov()
		];
	}

	private function makeIvanov(): UserData
	{
		$ivanov = new UserData();
		$ivanov->name = 'Иван Иванов';
		$ivanov->birthdate = now()->setDate(2025, 5, 20);
		$ivanov->photo = '/storage/ivanov.png';
		$ivanov->post = 'Директор';

		return $ivanov;
	}

	private function makePetrov(): UserData
	{
		$petrov = new UserData();
		$petrov->name = 'Арсений Петров';
		$petrov->birthdate = now()->setDate(2024, 9, 30);
		$petrov->photo = '/storage/petrov.png';
		$petrov->post = 'Дизайнер';

		return $petrov;
	}

	private function makeSidorov(): UserData
	{
		$petrov = new UserData();
		$petrov->name = 'Михаил Сидоров';
		$petrov->birthdate = now()->setDate(2024, 12, 31);
		$petrov->photo = '/storage/sidorov.png';
		$petrov->post = 'Уборщик';

		return $petrov;
	}
}
