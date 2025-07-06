<?php

namespace Tests\Unit\YandexWiki;

use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use PHPUnit\Framework\TestCase;

class MarkdownTableToArrayTest extends TestCase
{
	public function test_parsing_staff_details(): void
	{
		$markdown = $this->getStaffDetailsMd();

		$parser = new MarkdownParser($markdown);
		$parser->parse();

		$this->assertSame(
			$this->getStaffDetailsExpected(),
			$parser->tables[0]->getRows()
		);
	}

	public function test_parsing_birthdays(): void
	{
		$markdown = $this->getBirthdaysMd();

		$parser = new MarkdownParser($markdown);
		$parser->parse();

		$this->assertSame(
			$this->getBirthdaysExpected(),
			$parser->tables[0]->getRows()
		);
	}

	private function getStaffDetailsMd()
	{
		return file_get_contents(__DIR__ . '/resources/staff-details.txt');
	}

	private function getStaffDetailsExpected()
	{
		return json_decode(
			file_get_contents(__DIR__ . '/resources/staff-details-expected.json'),
			true,
			flags: JSON_THROW_ON_ERROR
		);
	}

	private function getBirthdaysMd()
	{
		return file_get_contents(__DIR__ . '/resources/birthdates.txt');
	}

	private function getBirthdaysExpected()
	{
		return json_decode(
			file_get_contents(__DIR__ . '/resources/birthdates-expected.json'),
			true,
			flags: JSON_THROW_ON_ERROR
		);
	}
}
