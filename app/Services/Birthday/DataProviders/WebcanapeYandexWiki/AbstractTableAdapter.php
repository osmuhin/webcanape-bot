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
			return $value;
		}

		return Normalizer::make($value)
			->htmlEntityDecode()
			->stripTags()
			->shrinkWhitespaces()
			->trim()
			->emptyStringToNull()
			->get();
	}

	protected function defineColumnOrder(array $headRow): void
	{
		$mapHeaderColumns = $this->getMapHeaderColumns();

		foreach ($mapHeaderColumns as $target => $columnName) {
			foreach ($headRow as $cellIdx => $value) {
				if (str_contains($value, $target)) {
					$this->columnOrder[$columnName] = $cellIdx;
					unset($headRow[$cellIdx]);
					unset($mapHeaderColumns[$target]);

					break 1;
				}
			}
		}
	}

	protected function getCell(array $row, string $cellName): ?string
	{
		return $row[$this->columnOrder[$cellName]];
	}

	protected function getNormalizedCell(array $row, string $cellName): ?string
	{
		return $this->normalizeCell(
			$this->getCell($row, $cellName)
		);
	}
}
