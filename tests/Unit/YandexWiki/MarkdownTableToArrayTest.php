<?php

namespace Tests\Unit\YandexWiki;

use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use App\Libs\YandexSdk\Wiki\MarkdownParser\TableState;
use PHPUnit\Framework\TestCase;

class MarkdownTableToArrayTest extends TestCase
{
	public function test_parsing_table_in_yandex_markdown()
	{
		$markdown = $this->getMdWithTable();

		// var_dump(preg_replace("/\n/", '\n', $markdown)); die;

		$parser = new MarkdownParser($markdown);
		$parser->parse();

		$this->assertCount(1, $parser->tables);
		$this->assertInstanceOf(TableState::class, $table = $parser->tables[0]);

		$this->assertSame([
			["\n\n**Row1-Column1**\n\n", "\n\n**Row1-Column2**\n\n"],
			["\n\nRow2-Column1\n\n", "\n\nRow2-Column2\n\n"],
			["\n\nRow3-Column1\n\n", "\n\nRow3-Column2\n\n"],
		], $table->getRows());

		// \n||\n\n**Row1-Column1**\n\n|\n\n**Row1-Column2**\n\n||\n||\n\nRow2-Column1\n\n|\n\nRow2-Column2\n\n||\n||\n\nRow3-Column1\n\n|\n\nRow3-Column2\n\n||\n|#\n\n&nbsp;\n\nSome|| epilog 321\n
	}

	private function getMdWithTable(): string
	{
		return <<<MD
Some prolog 123

#|
||

**Row1-Column1**

|

**Row1-Column2**

||
||

Row2-Column1

|

Row2-Column2

||
||

Row3-Column1

|

Row3-Column2

||
|#

&nbsp;

Some|| epilog 321

MD;
	}
}
