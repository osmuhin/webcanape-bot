<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\Contracts\DataProvider as DataProviderContract;
use Illuminate\Support\Collection;

class DataProvider implements DataProviderContract
{
	private string $birthdatesPageSlug;

	/** @var string[] */
	private array $staffDetailPages = [];

	private YandexWiki $wiki;

	private YandexWikiParser $parser;

	public function __construct(array $config)
	{
		$this->birthdatesPageSlug = $config['birthdates_page_slug'];
		$this->staffDetailPages = $config['staff_detail_pages'];
		$this->parser = new YandexWikiParser();
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
		foreach ($this->staffDetailPages as $slug) {
			$this->parser->handleStuffInfoTable(
				$this->fetchTable($slug)
			);
		}
	}
}
