<?php

namespace App\Console\Commands;

use App\Libs\YandexSdk\Wiki\GetPage;
use App\Libs\YandexSdk\Wiki\YandexWiki;
use Illuminate\Console\Command;

class SyncBirthdays extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sync-birthdays';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$yandexWiki = new YandexWiki(
			token: config('services.yandex.oauth_token'),
			orgId: config('services.yandex.org_id')
		);

		$getPageRequest = new GetPage(
			slug: 'hr/kontaktnye-dannye-sertifikaty-i-t.d.-sotrudnikov/dni-rozhdenija-sotrudnikov'
		);
		$getPageRequest->withField(GetPage::FIELD_CONTENT);

		$response = $yandexWiki->send($getPageRequest);

		dd($response->json());
	}
}
