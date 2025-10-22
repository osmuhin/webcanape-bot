<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\Contracts\DataProvider as DataProviderContract;
use App\Services\Birthday\UserData;
use Illuminate\Contracts\Config\Repository;

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

	public static function make(Repository $config): DataProviderContract
	{
		$provider = new self(
			config: $config->get('birthday.webcanape-yandex-wiki')
		);

		$wiki = new YandexWiki(
			token: $config->get('services.yandex.oauth_token'),
			orgId: $config->get('services.yandex.org_id')
		);

		$provider->setYandexWikiClient($wiki);

		return $provider;
	}

	public function setYandexWikiClient(YandexWiki $wiki): void
	{
		$this->wiki = $wiki;
	}

	public function getUsers(): array
	{
		return $this->combine(
			$this->fetchStaffTable(),
			$this->fetchBirthdayTable()
		);
	}

	/**
	 * @param \App\Services\Birthday\DataProviders\WebcanapeYandexWiki\EmployeeData[] $employeeCollection
	 * @param \App\Services\Birthday\DataProviders\WebcanapeYandexWiki\BirthdayData[] $birthdayCollection
	 *
	 * @return \App\Services\Birthday\UserData[]
	 */
	private function combine(array $employeeCollection, array $birthdayCollection): array
	{
		$users = [];

		foreach ($employeeCollection as $employee) {
			$user = new UserData();
			$user->name = $employee->firstName . ' ' . $employee->lastName;
			$user->photo = $employee->photo;
			$user->post = $employee->post;

			foreach ($birthdayCollection as $birthday) {
				if ($this->compare($birthday, $employee)) {
					$user->birthdate = $birthday->birthdate;
					$user->hiddenFromOther = preg_match("/без\s+рассылки/iu", $birthday->name);

					break;
				}
			}

			if (isset($user->birthdate)) {
				$users[] = $user;
			}
		}

		return $users;
	}

	private function compare(BirthdayData $birthday, EmployeeData $employee): bool
	{
		return $birthday->firstName == $employee->firstName &&
			$birthday->lastName == $employee->lastName;
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

	private function fetchBirthdayTable()
	{
		$adapter = new BirthdayTableAdapter(
			$this->fetchTable($this->birthdatesPageSlug)
		);

		return $adapter->transform();
	}
}
