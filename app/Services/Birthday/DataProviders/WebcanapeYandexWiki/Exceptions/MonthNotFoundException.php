<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki\Exceptions;

use Exception;

class MonthNotFoundException extends Exception
{
	public function __construct(string $month)
	{
		parent::__construct("Month '{$month}' not found");
	}
}
