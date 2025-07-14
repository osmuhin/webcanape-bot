<?php

namespace App\Libs\YandexSdk\Wiki\MarkdownParser;

use InvalidArgumentException;

class Parser
{
	/** @var \App\Libs\YandexSdk\Wiki\MarkdownParser\TableState[] */
	public array $tables = [];

	private TableState $currentTable;

	private bool $tableMode = false;

	public function __construct(private $stream)
	{
		if (!is_resource($stream)) {
			throw new InvalidArgumentException('Resource stream is expected.');
		}

		if (!stream_get_meta_data($stream)['mode'] || !str_contains(stream_get_meta_data($stream)['mode'], 'r')) {
			throw new InvalidArgumentException('Cannot read resource stream.');
		}
	}

	public function read()
	{
		while ($char = fgetc($this->stream)) {

		}
	}

	public function parse()
	{
		$lines = explode("\n", $this->markdown);

		foreach ($lines as $line) {
			$this->eachLine($line);
		}
	}

	private function eachLine(string $line)
	{
		$trimmedLine = trim($line);

		switch ($trimmedLine) {
			case '#|':
				return $this->switchTableMode(true);
			case '|#':
				return $this->switchTableMode(false);
		}

		if ($this->tableMode) {
			return $this->currentTable->handleLine($line, $trimmedLine);
		}
	}

	private function switchTableMode(bool $isOn)
	{
		$this->tableMode = $isOn;

		if ($isOn) {
			$this->currentTable = new TableState();
		} else {
			$this->tables[] = $this->currentTable;
		}
	}
}
