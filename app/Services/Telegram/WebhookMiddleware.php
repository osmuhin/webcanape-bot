<?php

namespace App\Services\Telegram;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookMiddleware
{
	public function __construct(private Telegram $telegram)
	{
		//
	}

	public function handle(Request $request, Closure $next): Response
	{
		if (!$token = $this->telegram->getWebhookSecretToken()) {
			return $next($request);
		}

		$tokenFromRequest = $request->header('X-Telegram-Bot-Api-Secret-Token');

		if ($tokenFromRequest !== $token) {
			$this->writeLog($request);

			abort(404);
		}

		return $next($request);
	}

	private function writeLog(Request $request): void
	{
		$message = <<<LOG
🤡 Какая-то пидота хотела сымитировать запрос от телеграма:
- IP: {ip}
- URL: {url}
- Body: {body}
LOG;
		$context = [
			'ip' => $request->ip(),
			'url' => $request->fullUrl(),
			'body' => $request->getContent()
		];

		Log::info($message, $context);
	}
}
