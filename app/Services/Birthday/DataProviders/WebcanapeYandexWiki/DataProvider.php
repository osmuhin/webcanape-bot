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
		$this->fetchStaffTable();

		return collect();
	}

	private function fetchTable(string $slug)
	{
		$getPageRequest = new GetPage(slug: $slug);
		$getPageRequest->withField(GetPage::FIELD_CONTENT);

		$content = $this->wiki->send($getPageRequest)->json()['content'];

		$parser = new MarkdownParser($content);
		$parser->parse();

		return $parser->tables[0];
	}

	private function fetchStaffTable()
	{
		$summarized = [];

		foreach ($this->staffDetailPages as $slug) {
			$adapter = new EmployeeTableAdapter(
				$this->fetchTable($slug)
			);

			$summarized = array_merge($summarized, $adapter->transform());
		}

		return $summarized;
	}
}
