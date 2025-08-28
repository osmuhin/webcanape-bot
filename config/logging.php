<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

	/*
	|--------------------------------------------------------------------------
	| Default Log Channel
	|--------------------------------------------------------------------------
	|
	| This option defines the default log channel that is utilized to write
	| messages to your logs. The value provided here should match one of
	| the channels present in the list of "channels" configured below.
	|
	*/

	'default' => env('LOG_CHANNEL', 'stack'),

	/*
	|--------------------------------------------------------------------------
	| Deprecations Log Channel
	|--------------------------------------------------------------------------
	|
	| This option controls the log channel that should be used to log warnings
	| regarding deprecated PHP and library features. This allows you to get
	| your application ready for upcoming major versions of dependencies.
	|
	*/

	'deprecations' => [
		'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
		'trace' => env('LOG_DEPRECATIONS_TRACE', false),
	],

	/*
	|--------------------------------------------------------------------------
	| Log Channels
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log channels for your application. Laravel
	| utilizes the Monolog PHP logging library, which includes a variety
	| of powerful log handlers and formatters that you're free to use.
	|
	| Available drivers: "single", "daily", "slack", "syslog",
	|                    "errorlog", "monolog", "custom", "stack"
	|
	*/

	'channels' => [

		'stack' => [
			'driver' => 'stack',
			'channels' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
			'ignore_exceptions' => false,
		],

		'sql' => [
			'driver' => 'daily',
			'days' => 2,
			'path' => runtime_path('logs/sql.log')
		],

		'single' => [
			'driver' => 'single',
			'path' => runtime_path('logs/laravel.log'),
			'level' => env('LOG_LEVEL', 'debug'),
			'replace_placeholders' => true
		],

		'emergency' => [
			'driver' => 'single',
			'path' => runtime_path('logs/emergency.log'),
			'level' => 'emergency',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'alert' => [
			'driver' => 'single',
			'path' => runtime_path('logs/alert.log'),
			'level' => 'alert',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'critical' => [
			'driver' => 'single',
			'path' => runtime_path('logs/critical.log'),
			'level' => 'critical',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'error' => [
			'driver' => 'single',
			'path' => runtime_path('logs/error.log'),
			'level' => 'error',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'warning' => [
			'driver' => 'single',
			'path' => runtime_path('logs/warning.log'),
			'level' => 'warning',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'notice' => [
			'driver' => 'single',
			'path' => runtime_path('logs/notice.log'),
			'level' => 'notice',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'info' => [
			'driver' => 'single',
			'path' => runtime_path('logs/info.log'),
			'level' => 'info',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'debug' => [
			'driver' => 'single',
			'path' => runtime_path('logs/debug.log'),
			'level' => 'debug',
			'bubble' => false,
			'replace_placeholders' => true
		],

		'daily' => [
			'driver' => 'daily',
			'path' => runtime_path('logs/laravel.log'),
			'level' => env('LOG_LEVEL', 'debug'),
			'days' => 14,
			'replace_placeholders' => true,
		],

		'stderr' => [
			'driver' => 'monolog',
			'level' => env('LOG_LEVEL', 'debug'),
			'handler' => StreamHandler::class,
			'formatter' => env('LOG_STDERR_FORMATTER'),
			'with' => [
				'stream' => 'php://stderr',
			],
			'processors' => [PsrLogMessageProcessor::class],
		],

		'syslog' => [
			'driver' => 'syslog',
			'level' => env('LOG_LEVEL', 'debug'),
			'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
			'replace_placeholders' => true,
		],

		'errorlog' => [
			'driver' => 'errorlog',
			'level' => env('LOG_LEVEL', 'debug'),
			'replace_placeholders' => true,
		],

		'null' => [
			'driver' => 'monolog',
			'handler' => NullHandler::class,
		]
	]
];
