<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Exceptions\StateInstanceNotFoundException;
use App\Libs\YandexSdk\Utilities\MarkdownParser\States\AbstractState;
use App\Libs\YandexSdk\Utilities\MarkdownParser\States\DefaultState;

class LexerContext
{
	private array $tokens = [];

	private array $states = [];

	private AbstractState $activeState;

	public function __construct(array $states)
	{
		$this->states = $states;
		$this->checkoutState(DefaultState::class);
	}

	public function pushToken(array $token)
	{
		$tokens[] = $token;
	}

	public function checkoutState(string $state)
	{
		if ($activeState = @$this->states[$state]) {
			$this->activeState = $activeState;
		} else {
			throw new StateInstanceNotFoundException($state);
		}
	}

	public function passThroughState(string $char)
	{
		$this->activeState->handle($char, $this);
	}

	public function getTokens(): array
	{
		return $this->tokens;
	}
}
