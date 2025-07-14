<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table;

use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class CellEndRecognizer extends CellStartRecognizer
{
	public function makeToken(): array
	{
		$this->reset();

		return [TokenType::TableCellEnd, null];
	}
}
