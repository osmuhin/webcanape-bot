<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser;

class Lexer
{
	private LexerContext $context;

	public function __construct()
	{
		$this->context = $this->makeContext();
	}

	public function tokenize(string $string)
	{
		$cursor = 0;
		$maxPosition = mb_strlen($string);

		while ($cursor < $maxPosition) {
			$this->context->passThroughState(
				$string[$cursor]
			);

			$cursor++;
		}

		return $this->context->getTokens();
	}

	private function makeContext(): LexerContext
	{
		return new LexerContext($this->initStates());
	}

	/**
	 * @return array<string, \App\Libs\YandexSdk\Utilities\MarkdownParser\States\AbstractState>
	 */
	private function initStates()
	{
		$recognizers = $this->initRecognizers();

		$stateClasses = [
			\App\Libs\YandexSdk\Utilities\MarkdownParser\States\DefaultState::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\States\InTable\TableStartState::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\States\InTable\RowStartState::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\States\InTable\CellStartState::class
		];
		$states = [];

		foreach ($stateClasses as $class) {
			$states[$class] = new $class($recognizers);
		}

		return $states;
	}

	/**
	 * @return array<string, \App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\AbstractRecognizer>
	 */
	private function initRecognizers(): array
	{
		$recognizerClasses = [
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\TextRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\TableStartRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\TableEndRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\RowStartRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\RowEndRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\CellStartRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\CellEndRecognizer::class
		];
		$recognizers = [];

		foreach ($recognizerClasses as $class) {
			$recognizers[$class] = new $class();
		}

		return $recognizers;
	}
}
