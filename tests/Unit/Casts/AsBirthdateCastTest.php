<?php

namespace Tests\Unit\Casts;

use App\Casts\AsBirthdate;
use App\Models\User;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

class AsBirthdateCastTest extends TestCase
{
	public function test_get()
	{
		$cast = new AsBirthdate();
		$date = $cast->get(new User(), '', '04-8', []);

		assertInstanceOf(Carbon::class, $date);
		assertSame(4, $date->day);
		assertSame(8, $date->month);
	}

	public function test_get_incorrect_birthdate_string()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Incorrect birthdate string; It must have the "dd-mm" format.');

		$cast = new AsBirthdate();
		$cast->get(new User(), '', '123123', []);
	}

	public function test_get_incorrect_birthdate_object()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Expected value of type "string", but received type "object".');

		$cast = new AsBirthdate();
		$cast->get(new User(), '', (object) [], []);
	}

	public function test_set()
	{
		$date = Carbon::createFromDate(month: 3, day: 30);

		$cast = new AsBirthdate();
		$dateString = $cast->set(new User(), '', $date, []);

		assertSame('30-03', $dateString);
	}

	public function test_set_incorrect_birthdate_type()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unknown value type: integer');

		$cast = new AsBirthdate();
		$cast->set(new User(), '', 123, []);
	}
}
