<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\States;

use App\Libs\YandexSdk\Utilities\MarkdownParser\LexerContext;

abstract class AbstractState
{
	abstract public function handle(string $char, LexerContext $context);
}
