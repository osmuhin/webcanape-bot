<?php

namespace App\Libs\YandexSdk\Wiki;

use App\Libs\YandexSdk\YandexSdk;

class YandexWiki extends YandexSdk
{
	public function resolveBaseUrl(): string
	{
		return 'https://api.wiki.yandex.net';
	}
}
