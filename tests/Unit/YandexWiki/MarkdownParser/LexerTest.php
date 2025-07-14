<?php

namespace Tests\Unit\YandexWiki\MarkdownParser;

use App\Libs\YandexSdk\Utilities\MarkdownParser\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
	public function test_tokenization_string()
	{
		$string = "he#llo#|\n||\nCell-Row1-Col1\n|\nCell-with-**bold**-text-Row1-Col2\n||\n||\nCell-Row2-Col1\n|\nCell-Row2-Col2\n|||#";

		/**
		 * Результат токенизации:
		 *
		 * ['Text', 'he#llo'],
		 * ['TableStart', null],
		 * ['TableRowStart', null],
		 * ['TableCellStart', null],
		 * ['Text', "\nCell-Row1-Col1\n"],
		 * ['TableCellEnd', null],
		 * ['TableCellStart', null],
		 * ['Text', "\nCell-with-**bold**-text-Row1-Col2\n"],
		 * ['TableCellEnd', null],
		 * ['TableRowEnd', null],
		 * ['TableRowStart', null],
		 * ['TableCellStart', null],
		 * ['Text', "\nCell-Row2-Col1\n"],
		 * ['TableCellEnd', null],
		 * ['TableCellStart', null],
		 * ['Text', "\nCell-Row2-Col2\n"],
		 * ['TableCellEnd', null],
		 * ['TableRowEnd', null],
		 * ['TableEnd', null]
		 */

		$lexer = new Lexer();
		$tokens = $lexer->tokenize($string);

		dd($tokens);
	}
}
