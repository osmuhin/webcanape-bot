<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Services\Birthday\DataProviders\WebcanapeYandexWiki\Exceptions\MonthNotFoundException;
use Carbon\Carbon;

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
			'ФИО' => 'fullName'
		];
	}

	private function makeDto(array $row): BirthdayData
	{
		$dto = new BirthdayData();
		$dto->birthdate = $this->castDate(
			$this->getCell($row, 'birthdate')
		);

		[$dto->firstName, $dto->lastName] = $this->splitFullName(
			$this->getCell($row, 'fullName')
		);

		return $dto;
	}

	private function castDate(string $date)
	{
		$monthes = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
		[$day, $month] = explode(' ', $date);
		$monthIdx = array_search($month, $monthes);

		if ($monthIdx === false) {
			throw new MonthNotFoundException($month);
		}

		return now()->setDate(date('Y'), $monthIdx + 1, $day);
	}
}
