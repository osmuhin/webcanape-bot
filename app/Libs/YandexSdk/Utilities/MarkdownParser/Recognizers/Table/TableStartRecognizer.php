<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\AbstractRecognizer;
use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class TableStartRecognizer extends AbstractRecognizer
{
	protected const SET = ['#', '|'];

	protected const ACTIVATION_INDEX = 2;

	public function makeToken(): array
	{
		$this->reset();

		return [TokenType::TableStart, null];
	}

	protected function compare(string $char): bool
	{
		return $char === self::SET[$this->matchIndex];
	}

	protected function activation(): bool
	{
		return $this->matchIndex >= self::ACTIVATION_INDEX;
	}
}
