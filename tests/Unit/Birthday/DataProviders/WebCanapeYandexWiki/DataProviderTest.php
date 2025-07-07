<?php

namespace Tests\Unit\Birthday\DataProviders\WebCanapeYandexWiki;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use App\Services\Birthday\DataProviders\WebcanapeYandexWiki\DataProvider;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

class DataProviderTest extends TestCase
{
	public function test_can_get_users()
	{
		$mockClient = new MockClient([
			GetPage::class => function (PendingRequest $pendingRequest) {
				$slug = $pendingRequest->getRequest()->query()->get('slug');

				switch ($slug) {
					case 'spisok-i-kontaktnye-dannye-sotrudnikov/administracija':
						return MockResponse::make(body: json_encode([
							'content' => $this->getResource('staff-details.txt')
						]));
					case 'spisok-i-kontaktnye-dannye-sotrudnikov/administracija2':
						return MockResponse::make(body: json_encode([
							'content' => $this->getResource('birthdates.txt')
						]));
					case 'hr/kontaktnye-dannye-sertifikaty-i-t.d.-sotrudnikov/dni-rozhdenija-sotrudnikov':
						return MockResponse::make(body: json_encode([
							'content' => $this->getResource('birthdates.txt')
						]));
				}
			}
		]);

		$wiki = new YandexWiki('test', 1);
		$wiki->withMockClient($mockClient);

		$provider = new DataProvider(
			config: $this->getProviderConfig()
		);
		$provider->setYandexWikiClient($wiki);

		$users = $provider->getUsers();


	}

	private function getProviderConfig()
	{
		return [
			'birthdates_page_slug' => 'hr/kontaktnye-dannye-sertifikaty-i-t.d.-sotrudnikov/dni-rozhdenija-sotrudnikov',
			'staff_detail_pages' => [
				'spisok-i-kontaktnye-dannye-sotrudnikov/administracija',
				'spisok-i-kontaktnye-dannye-sotrudnikov/administracija2',
			],
			'yandex_oauth_token' => 'test',
			'yandex_org_id' => 1
		];
	}

	private function getResource(string $file)
	{
		return file_get_contents(
			__DIR__ . "/../../../.resources/{$file}"
		);
	}
}
