<?php

namespace App\Libs\YandexSdk\Wiki\MarkdownParser;

use Iterator;
use ReturnTypeWillChange;

class TableState implements Iterator
{
	private array $rows = [];

	private bool $inRow = false;

	private int $columnIdx = 0;

	private int $rowIdx = 0;

	private int $iteratorPosition = 0;

	public function rewind(): void
	{
		$this->iteratorPosition = 0;
	}

	#[ReturnTypeWillChange]
	public function current(): mixed
	{
		return $this->rows[$this->iteratorPosition];
	}

	#[ReturnTypeWillChange]
	public function key(): mixed
	{
		return $this->iteratorPosition;
	}

	public function next(): void {
		$this->iteratorPosition++;
	}

	public function valid(): bool
	{
		return isset($this->rows[$this->iteratorPosition]);
	}

	public function handleLine(string $line): void
	{
		if ($line === '||') {
			$this->checkoutRow();

			return;
		}

		if ($line === '|') {
			$this->columnIdx++;

			return;
		}

		if ($this->inRow) {
			$this->setCell($line);
		}
	}

	public function getRows()
	{
		return $this->rows;
	}

	private function checkoutRow()
	{
		if ($this->inRow = !$this->inRow) {
			$this->columnIdx = 0;
			$this->rows[$this->rowIdx] = [''];
		} else {
			$this->rowIdx++;
		}
	}

	private function setCell(string $value): void
	{
		if (isset($this->rows[$this->rowIdx][$this->columnIdx])) {
			$this->rows[$this->rowIdx][$this->columnIdx] .= $value;
		} else {
			$this->rows[$this->rowIdx][$this->columnIdx] = $value;
		}
	}
}
