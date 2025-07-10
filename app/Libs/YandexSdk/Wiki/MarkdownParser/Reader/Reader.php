<?php

namespace App\Libs\YandexSdk\Wiki\MarkdownParser\Reader;

class Reader
{
	public const START_TABLE = 1;

	public const END_TABLE = 2;

	public const TABLE_ROW_DELIMITER = 3;

	private Processor $processor;



	public function open($stream): void
	{
		$this->processor = new Processor($stream);
	}

	public function read(): bool
	{
		fseek($this->stream, $this->cursor);

		while (!feof($this->stream)) {

		}

		return true;
	}

	private function nextChar()
	{

	}
}
