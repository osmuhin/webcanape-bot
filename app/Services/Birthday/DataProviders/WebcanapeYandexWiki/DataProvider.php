<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\MarkdownParser\MarkdownParser;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\Contracts\DataProvider as DataProviderContract;
use App\Services\Birthday\UserData;

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
	 */
	private function combine(array $employeeCollection, array $birthdayCollection): array
	{
		$users = [];

		foreach ($employeeCollection as $employee) {
			$user = new UserData();
			$user->firstName = $employee->firstName;
			$user->lastName = $employee->lastName;
			$user->photo = $employee->photo;
			$user->post = $employee->post;

			foreach ($birthdayCollection as $birthday) {
				if (
					$birthday->firstName == $employee->firstName &&
					$birthday->lastName == $employee->lastName
				) {
					$user->birthdate = $birthday->birthdate;

					break;
				}
			}

			if (isset($user->birthdate)) {
				$users[] = $user;
			}
		}

		return $users;
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
