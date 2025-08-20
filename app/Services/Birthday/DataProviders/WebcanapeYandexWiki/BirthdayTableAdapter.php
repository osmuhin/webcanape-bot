<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use InvalidArgumentException;

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
		$dto->name = $this->getCell($row, 'name');
		$dto->birthdate = $this->castDate(
			$this->getCell($row, 'birthdate')
		);
		[$dto->firstName, $dto->lastName] = split_full_name(
			$this->getCell($row, 'name')
		);

		return $dto;
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function castDate(string $date)
	{
		$monthes = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
		[$day, $month] = explode(' ', $date);
		$monthIdx = array_search($month, $monthes);

		if ($monthIdx === false) {
			throw new InvalidArgumentException("Month '{$month}' not found");
		}

		return now()->setDate(date('Y'), $monthIdx + 1, $day);
	}
}
