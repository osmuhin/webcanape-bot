<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\MarkdownParser\TableState;
use Illuminate\Support\Arr;

abstract class AbstractTableAdapter
{
	protected array $columnOrder = [];

	public function __construct(protected TableState $table)
	{
		//
	}

	abstract public function transform(): array;

	abstract protected function getMapHeaderColumns(): array;

	protected function normalizeCell(?string $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = preg_replace('/\x{A0}/u', ' ', $value);
		$value = trim($value, characters: " \n\r\t\v\0*");
		$value = preg_replace('/\s{2,}/u', ' ', $value);

		return $value === '' ? null : $value;
	}

	protected function splitFullName(string $fullName): array
	{
		$exploded = explode(' ', $fullName, 2);

		$firstName = $exploded[0];
		$lastName = '';

		if (isset($exploded[1])) {
			$lastName = explode(' ', $exploded[1])[0];
		}

		return [$firstName, $lastName];
	}

	protected function defineColumnOrder(array $headRow): void
	{
		$mapHeaderColumns = $this->getMapHeaderColumns();

		foreach ($headRow as $cellIdx => $cellValue) {
			$value = $this->normalizeCell($cellValue);

			if ($columnName = Arr::get($mapHeaderColumns, $value)) {
				$this->columnOrder[$columnName] = $cellIdx;
			}
		}
	}

	protected function getCell(array $row, string $cellName): ?string
	{
		return $this->normalizeCell(
			$row[$this->columnOrder[$cellName]]
		);
	}
}
