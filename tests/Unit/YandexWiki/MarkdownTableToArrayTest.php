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

		$parser = new MarkdownParser($markdown);
		$parser->parse();

		$this->assertCount(1, $parser->tables);
		$this->assertInstanceOf(TableState::class, $table = $parser->tables[0]);

		$this->assertSame([
			["**Row1-Column1**", "**Row1-Column2**"],
			["Row2-Column1", "Row2-Column2"],
			["Row3-Column1", "Row3-Column2"],
		], $table->getRows());
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
