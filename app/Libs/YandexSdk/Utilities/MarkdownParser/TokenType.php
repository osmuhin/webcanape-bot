<?php

namespace App\Libs\YandexSdk\Utilities\MarkdownParser;

enum TokenType
{
	case TableStart;

	case TableEnd;

	case TableRowStart;

	case TableRowEnd;

	case Text;

	case TableCellStart;

	case TableCellEnd;
}


// #|
// ||
// Cell-Row1-Col1
// |
// Cell-with-**bold**-text-Row1-Col2
// ||
// ||
// Cell-Row2-Col1
// |
// Cell-Row2-Col2
// ||
// |#
