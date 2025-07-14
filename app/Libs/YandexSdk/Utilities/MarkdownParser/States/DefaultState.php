<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\States;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Exceptions\MissingRequiredRecognizerException;
use App\Libs\YandexSdk\Utilities\MarkdownParser\LexerContext;

class DefaultState extends AbstractState
{
	protected array $recognizers = [];

	public function __construct(array $recognizers)
	{
		foreach ($this->requiredRecognizers() as $req) {
			if (isset($recognizers[$req])) {
				$this->recognizers[] = $req;
			} else {
				throw new MissingRequiredRecognizerException($req);
			}
		}
	}

	public static function requiredRecognizers(): array
	{
		return [
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\TextRecognizer::class,
			\App\Libs\YandexSdk\Utilities\MarkdownParser\Recognizers\Table\TableStartRecognizer::class,
		];
	}

	public function handle(string $char, LexerContext $context)
	{
		dd('handle', $char);
	}
}
