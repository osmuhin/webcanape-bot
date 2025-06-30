<?php

namespace App\Libs\YandexSdk;

use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;

abstract class YandexSdk extends Connector
{
	public function __construct(protected string $token, protected string $orgId)
	{

	}

	protected function defaultHeaders(): array
	{
		return [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'X-Org-Id' => $this->orgId
		];
	}

	protected function defaultAuth(): HeaderAuthenticator
    {
        return new HeaderAuthenticator("OAuth {$this->token}");
    }
}
