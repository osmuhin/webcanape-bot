<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Mailgun, Postmark, AWS and more. This file provides the de facto
	| location for this type of information, allowing packages to have
	| a conventional file to locate the various service credentials.
	|
	*/

	'yandex' => [
		'oauth_token' => env('YANDEX_OAUTH_TOKEN'),
		'org_id' => env('YANDEX_ORG_ID')
	],

	'telegram' => [
		'bot_token' => env('TELEGRAM_BOT_TOKEN'),
		'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
		'webhook_base_url' => env('TELEGRAM_WEBHOOK_BASE_URL', env('APP_URL')),
		'webhook_url' => '/telegram/webhook',
		'webhook_secret_token' => env('TELEGRAM_WEBHOOK_SECRET_TOKEN')
	],

	'telegram-bot-api' => [
		'token' => env('TELEGRAM_BOT_TOKEN')
	]
];
