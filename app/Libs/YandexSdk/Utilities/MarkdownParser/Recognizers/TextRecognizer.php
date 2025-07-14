<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers;

use App\Libs\YandexSdk\Utilities\MarkdownParser\TokenType;

class TextRecognizer extends AbstractRecognizer
{
	public function compare(string $_): bool
	{
		return true;
	}

	public function makeToken(): array
	{
		$token = [TokenType::Text, $this->buffer];
		$this->reset();

		return $token;
	}
}
