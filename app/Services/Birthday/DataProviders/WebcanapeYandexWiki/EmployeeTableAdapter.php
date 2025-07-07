<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use Illuminate\Support\Collection;

class EmployeeTableAdapter extends AbstractTableAdapter
{
	/**
	 * @return \Illuminate\Support\Collection<int, \App\Services\Birthday\DataProviders\WebcanapeYandexWiki\EmployeeData>
	 */
	public function transform(): Collection
	{
		$collection = collect();

		foreach ($this->table as $idx => $row) {
			if ($idx === 0) {
				$this->defineColumnOrder($row);

				continue;
			}

			$collection->push(
				$this->makeDto($row)
			);
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
		$dto->fullName = $row[$this->columnOrder['fullName']];
	}
}
