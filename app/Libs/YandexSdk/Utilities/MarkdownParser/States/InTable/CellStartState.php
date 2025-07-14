<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\States\InTable;

use App\Libs\YandexSdk\Utilities\MarkdownParser\States\AbstractState;

class CellStartState extends AbstractState
{
	protected array $recognizers = [];

	public function __construct(array $recognizers)
	{

	}

	public static function requiredRecognizers(): array
	{
		return [];
	}

	public function handle(string $char)
	{

	}
}
