<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\MarkdownParser\TableState;
use App\Services\Birthday\UserData;
use Illuminate\Support\Str;

class YandexWikiParser
{
	private array $stuffInfo = [];

	private array $mapStaffInfoColumns = [
		'ФИО' => 'full_name',
		'Должность' => 'post',
		'Фото' => 'photo',
	];

	private array $mapBirthdaysColumns = [
		'Дата' => 'birthdate',
		'ФИО' => 'full_name'
	];

	public function __construct()
	{

	}

	public function handleStuffInfoTable(TableState $table)
	{
		$order = [];

		foreach ($table as $idx => $row) {
			if ($idx === 0) {
				$order = $this->defineStuffInfoColumnsOrder($row);

				continue;
			}

			$this->writeRow($row, $order);
		}

		dd($this->stuffInfo);
	}

	private function defineStuffInfoColumnsOrder(array $row)
	{
		$order = [];

		foreach ($row as $cellIdx => $cellValue) {
			$value = $this->normalizeCell($cellValue);

			if ($columnName = @$this->mapStaffInfoColumns[$value]) {
				$order[$cellIdx] = $columnName;
			}
		}

		return $order;
	}

	private function writeRow(array $row, array $order)
	{
		$user = [];

		foreach ($row as $cellIdx => $value) {
			if ($columnName = @$order[$cellIdx]) {
				$user[$columnName] = $this->normalizeCell($value);
			}
		}

		$this->stuffInfo[] = $user;
	}
}
