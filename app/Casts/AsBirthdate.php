<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AsBirthdate implements CastsAttributes
{
	public function get(Model $model, string $key, mixed $value, array $attributes): Carbon
	{
		if (!is_string($value)) {
			$type = gettype($value);

			throw new InvalidArgumentException("Expected value of type \"string\", but received type \"{$type}\".");
		}

		if (!str_contains($value, '-')) {
			throw new InvalidArgumentException('Incorrect birthdate string; It must have the "dd-mm" format.');
		}

		[$day, $month] = explode('-', $value, 2);

		return Carbon::createFromDate(month: (int) $month, day: (int) $day, timezone: 'Europe/Moscow');
	}

	public function set(Model $model, string $key, mixed $value, array $attributes): string
	{
		if (is_string($value)) {
			return $value;
		}

		if ($value instanceof Carbon) {
			return $value->format('d-m');
		}

		$type = gettype($value);

		throw new InvalidArgumentException("Unknown value type: {$type}");
	}
}
