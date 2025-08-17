<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use Illuminate\Support\Arr;

use function Illuminate\Filesystem\join_paths;

class EmployeeTableAdapter extends AbstractTableAdapter
{
	public const PHOTO_BASE_URL = 'https://wiki.yandex.ru';

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

			if ($this->getCell($row, 'post')) {
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
		$dto->name = $this->getCell($row, 'name');
		$dto->post = $this->getCell($row, 'post');
		$dto->photo = $this->normalizePhoto(
			$this->getCell($row, 'photo')
		);

		[$dto->firstName, $dto->lastName] = $this->splitFullName(
			$this->getCell($row, 'name')
		);

		return $dto;
	}

	/**
	 * @param string $mdPhoto Example: ![Иванов (Директор).png](/storage/ivanov.png =349x)
	 */
	private function normalizePhoto(?string $mdPhoto): ?string
	{
		if (!$mdPhoto) {
			return null;
		}

		$photo = preg_replace("/\!\[.*?]/", '', $mdPhoto);
		preg_match("/\((?'url'.*?)\s+=.*\)/", $photo, $matches);

		if ($url = Arr::get($matches, 'url')) {
			$url = join_paths(self::PHOTO_BASE_URL, $url);
		}

		return $url;
	}
}
