<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\Contracts\DataProvider;
use Illuminate\Support\Collection;

class WebcanapeYandexWikiDataProvider implements DataProvider
{
	private string $birthdatesPageSlug;

	/** @var string[] */
	private array $staffDetailPages = [];

	private YandexWiki $wiki;

	public function __construct(array $config)
	{
		$this->birthdatesPageSlug = $config['birthdates_page_slug'];
		$this->staffDetailPages = $config['staff_detail_pages'];
	}

	public function setYandexWikiClient(YandexWiki $wiki)
	{
		$this->wiki = $wiki;
	}

	public function getUsers(): Collection
	{
		$collection = collect();

		$this->fetchStaffTable();

		return $collection;
	}

	private function fetchTable(string $slug)
	{
		$getPageRequest = new GetPage(slug: $this->staffDetailPages[0]);
		$getPageRequest->withField(GetPage::FIELD_CONTENT);

		$content = $this->wiki->send($getPageRequest)->json()['content'];

		$parser = new MarkdownParser($content);
		$parser->parse();

		return $parser->tables[0];
	}

	private function fetchStaffTable()
	{
		$table = [];

		foreach ($this->staffDetailPages as $slug) {
			$table[] = $this->fetchTable($slug);
		}

		return $table;
	}

	private function extractStaffInfo()
	{

	}
}
