<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser\Exceptions;

use Exception;

class MissingRequiredRecognizerException extends Exception
{
	public function __construct(string $requiredRecognizer)
	{
		parent::__construct("Missing required recognizer {$requiredRecognizer}.");
	}
}
