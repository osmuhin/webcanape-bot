<?php

namespace App\Libs\YandexSdk\Wiki;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetPage extends Request
{
	public const FIELD_CONTENT = 'content';

	public const FIELD_ATTRIBUTES = 'attributes';

	protected Method $method = Method::GET;

	protected array $fields = [];

	public function __construct(protected string $slug)
	{

	}

	public function resolveEndpoint(): string
    {
        return '/v1/pages';
    }

	public function withField(string $field): self
	{
		if (!in_array($field, $this->fields)) {
			$this->fields[] = $field;
		}

		return $this;
	}

	protected function defaultQuery(): array
    {
        return [
            'slug' => $this->slug,
            'fields' => $this->buildFields()
        ];
    }

	protected function buildFields(): string
	{
		return implode(',', $this->fields);
	}
}
