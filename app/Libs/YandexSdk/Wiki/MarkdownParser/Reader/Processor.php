<?php

namespace App\Libs\YandexSdk\Wiki\MarkdownParser\Reader;

use InvalidArgumentException;

class Processor
{
	private string $stream;

	private int $cursor;

	private string $encoding;

	public function __construct(string $stream, string $encoding = 'utf-8')
	{
		if (!is_resource($stream)) {
			throw new InvalidArgumentException('Resource stream is expected.');
		}

		if (
			!stream_get_meta_data($stream)['mode'] ||
			!str_contains(stream_get_meta_data($stream)['mode'], 'r')
		) {
			throw new InvalidArgumentException('Cannot read resource stream.');
		}

		$this->stream = $stream;
		$this->encoding = $encoding;
	}

	public function read(): string
	{

	}

	public function nextChar(): string
	{
		$buffer = '';

		while (!feof($this->stream)) {
			$buffer .= fgetc($this->stream);

			if (mb_check_encoding($buffer, 'UTF-8')) {
				return $buffer;
			}
		}

		$char = fgetc($this->stream);

		fseek($this->stream, $this->cursor++);

		return $char;
	}
}
