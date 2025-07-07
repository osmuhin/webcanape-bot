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

			$collection[] = $this->makeDto($row);
		}

		return $collection;
	}

	protected function getMapHeaderColumns(): array
	{
		return [
			'ФИО' => 'fullName',
			'Должность' => 'post',
			'Фото' => 'photo',
		];
	}

	private function makeDto(array $row): EmployeeData
	{
		$dto = new EmployeeData();
		$dto->fullName = $this->getCell($row, 'fullName');
		$dto->post = $this->getCell($row, 'post');
		$dto->photo = $this->getCell($row, 'photo');

		return $dto;
	}
}
