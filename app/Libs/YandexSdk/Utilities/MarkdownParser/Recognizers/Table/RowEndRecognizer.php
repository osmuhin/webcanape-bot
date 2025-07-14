<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table;

use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class RowEndRecognizer extends RowStartRecognizer
{
	public function makeToken(): array
	{
		$this->reset();

		return [TokenType::TableRowEnd, null];
	}
}
