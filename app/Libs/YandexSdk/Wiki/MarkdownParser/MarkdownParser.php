<?php

namespace App\Libs\YandexSdk\Wiki\MarkdownParser;

class MarkdownParser
{
	/** @var \App\Libs\YandexSdk\Wiki\MarkdownParser\TableState[] */
	public array $tables = [];

	private TableState $currentTable;

	private bool $tableMode = false;

	public function __construct(private string $markdown)
	{

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
			return $this->currentTable->handleLine($trimmedLine);
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
