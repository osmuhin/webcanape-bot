<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use function PHPUnit\Framework\assertSame;

class HelpersTest extends TestCase
{
	#[Test]
	public function it_splits_full_name(): void
	{
		assertSame(['Иван', 'Иванов'], split_full_name("\tИван  \t \r    Иванов\n\n  Иванович "));
		assertSame(['Иван', 'Иванов'], split_full_name("Иванов Иван  \t \r    \n\n  Иванович ", false));
	}
}
