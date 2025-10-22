<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use InvalidArgumentException;

use function Illuminate\Filesystem\join_paths;

class Normalizer
{
	public const PHOTO_BASE_URL = 'https://wiki.yandex.ru';

	public function __construct(private ?string $string)
	{
		//
	}

	public static function make(string $input): self
	{
		return new static($input);
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public static function getDate(string $input): Carbon
	{
		$date = preg_replace('/[^\p{Cyrillic}\s\p{Pd}\d]+/u', ' ', $input);
		$date = static::make($date)->shrinkWhitespaces()->trim()->get();

		$monthes = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
		[$day, $month] = explode(' ', $date);
		$monthIdx = array_search($month, $monthes);

		if ($monthIdx === false) {
			throw new InvalidArgumentException("Month '{$month}' not found");
		}

		return now()->setDate(date('Y'), $monthIdx + 1, $day);
	}

	public static function getName(string $input): string
	{
		$name = preg_replace('/[^\p{Cyrillic}\s\p{Pd}]+/u', ' ', $input);

		return static::make($name)->shrinkWhitespaces()->trim()->get();
	}

	/**
	 * @param string $mdPhoto Example: ![Иванов (Директор).png](/storage/ivanov.png =349x)
	 */
	public static function getPhoto(string $input): string
	{
		$photo = static::make($input)
			->htmlEntityDecode()
			->stripTags()
			->shrinkWhitespaces()
			->trim()
			->emptyStringToNull()
			->get();

		$photo = preg_replace("/\!\[.*?]/", '', $photo);
		preg_match("/\((?'url'.*?)\s+=.*\)/", $photo, $matches);

		if ($url = Arr::get($matches, 'url')) {
			$url = join_paths(self::PHOTO_BASE_URL, $url);
		}

		return $url;
	}

	public function emptyStringToNull(): self
	{
		if ($this->string === '') {
			$this->string = null;
		}

		return $this;
	}

	public function htmlEntityDecode(): self
	{
		if ($this->string !== null) {
			$this->string = html_entity_decode($this->string);
		}

		return $this;
	}

	public function stripTags(): self
	{
		if ($this->string !== null) {
			$this->string = strip_tags($this->string);
		}

		return $this;
	}

	public function trim(): self
	{
		if ($this->string !== null) {
			$this->string = trim($this->string);
		}

		return $this;
	}

	public function shrinkWhitespaces()
	{
		if ($this->string !== null) {
			// заменяет все неразрывные пробелы на обычные пробелы
			$this->string = preg_replace('/\x{A0}/u', ' ', $this->string);
			$this->string = preg_replace('/\s{2,}/u', ' ', $this->string);
		}

		return $this;
	}

	public function get(): ?string
	{
		if ($this->string === null) {
			return null;
		}

		return $this->string;
	}
}
