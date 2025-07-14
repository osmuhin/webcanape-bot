<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\AbstractRecognizer;
use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class CellStartRecognizer extends AbstractRecognizer
{
	protected bool $escapeFlag = false;

	protected function compare(string $char): bool
	{
		if ($this->escapeFlag) {
			return $this->escapeFlag = false;
		}

		if ($this->escapeFlag = $char === "\\") {
			return false;
		}

		return $char === '|';
	}

	public function makeToken(): array
	{
		$this->reset();

		return [TokenType::TableCellStart, null];
	}
}
