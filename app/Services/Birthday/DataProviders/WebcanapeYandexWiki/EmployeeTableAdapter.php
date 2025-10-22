<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

class EmployeeTableAdapter extends AbstractTableAdapter
{
	/**
	 * @return \App\Services\Birthday\DataProviders\WebcanapeYandexWiki\EmployeeData[]
	 */
	public function transform(): array
	{
		$collection = [];

		foreach ($this->table as $idx => $row) {
			if ($idx === 0) {
				$this->defineColumnOrder($row);

				continue;
			}

			if ($this->getNormalizedCell($row, 'post')) {
				$collection[] = $this->makeDto($row);
			}
		}

		return $collection;
	}

	protected function getMapHeaderColumns(): array
	{
		return [
			'ФИО' => 'name',
			'Должность' => 'post',
			'Фото' => 'photo',
		];
	}

	private function makeDto(array $row): EmployeeData
	{
		$dto = new EmployeeData();
		$dto->name = Normalizer::getName(
			$this->getCell($row, 'name')
		);

		$dto->post = $this->getNormalizedCell($row, 'post');

		$dto->photo = Normalizer::getPhoto(
			$this->getCell($row, 'photo')
		);

		[$dto->firstName, $dto->lastName] = split_full_name($dto->name);

		return $dto;
	}
}
