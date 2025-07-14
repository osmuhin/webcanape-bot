<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers;

abstract class AbstractRecognizer
{
	protected int $matchIndex = 0;

	protected string $buffer = '';

	abstract public function makeToken(): array;

	abstract protected function compare(string $char): bool;

	public function input(string $char): ?array
	{
		$comparisonResult = (int) $this->compare($char);
		$this->buffer .= $char;
		$this->matchIndex = ($this->matchIndex + 1)*$comparisonResult;

		return $this->activation() ? $this->makeToken() : null;
	}

	protected function activation(): bool
	{
		return false;
	}

	protected function reset()
	{
		$this->buffer = '';
		$this->matchIndex = 0;
	}
}
