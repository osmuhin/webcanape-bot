<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Exceptions;

use Exception;

class StateInstanceNotFoundException extends Exception
{
	public function __construct(string $state)
	{
		parent::__construct("State instance \"{$state}\" not registered.");
	}
}
