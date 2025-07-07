<?php

namespace App\Services\Birthday\DataProviders\WebcanapeYandexWiki;

use Illuminate\Support\Collection;

class BirthdateTableAdapter extends AbstractTableAdapter
{
	public function transform(): Collection
	{
		return collect();
	}
}
