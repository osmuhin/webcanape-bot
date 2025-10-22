<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

class BirthdayTableAdapter extends AbstractTableAdapter
{
	public function transform(): array
	{
		$collection = [];

		foreach ($this->table as $idx => $row) {
			if ($idx === 0) {
				$this->defineColumnOrder($row);

				continue;
			}

			$collection[] = $this->makeDto($row);
		}

		return $collection;
	}

	protected function getMapHeaderColumns(): array
	{
		return [
			'Дата' => 'birthdate',
			'ФИО' => 'name'
		];
	}

	private function makeDto(array $row): BirthdayData
	{
		$dto = new BirthdayData();

		if ($this->getCell($row, 'name') === null) {
			dd($this->getCell($row, 'name'));
		}

		$dto->name = Normalizer::getName(
			$this->getCell($row, 'name')
		);

		$dto->birthdate = Normalizer::getDate(
			$this->getCell($row, 'birthdate')
		);

		[$dto->firstName, $dto->lastName] = split_full_name(
			$dto->name
		);

		return $dto;
	}
}
