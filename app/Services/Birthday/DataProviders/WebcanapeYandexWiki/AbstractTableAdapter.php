<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\MarkdownParser\TableState;
use Illuminate\Support\Collection;

abstract class AbstractTableAdapter
{
	protected array $columnOrder = [];

	public function __construct(protected TableState $table)
	{
		//
	}

	abstract public function transform(): Collection;

	abstract protected function getMapHeaderColumns(): array;

	protected function normalizeCell(string $value): string
	{
		$value = preg_replace('/\x{A0}/u', ' ', $value);
		$value = trim($value, characters: " \n\r\t\v\0*");

		return preg_replace('/\s{2,}/u', ' ', $value);
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

			if ($columnName = @$mapHeaderColumns[$value]) {
				$this->columnOrder[$columnName] = $cellIdx;
			}
		}
	}
}
