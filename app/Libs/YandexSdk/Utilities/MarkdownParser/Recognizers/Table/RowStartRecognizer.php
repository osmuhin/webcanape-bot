<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\AbstractRecognizer;
use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class RowStartRecognizer extends AbstractRecognizer
{
	protected const SET = ['|', '|'];

	protected const ACTIVATION_INDEX = 2;

	protected function compare(string $char): bool
	{
		return $char === self::SET[$this->matchIndex];
	}

	public function makeToken(): array
	{
		$this->reset();

		return [TokenType::TableRowStart, null];
	}
}
