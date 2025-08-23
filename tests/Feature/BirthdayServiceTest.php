<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Birthday\Birthday;
use App\Services\Birthday\Contracts\DataProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\BirthdayDataProvider;
use Tests\TestCase;

use function PHPUnit\Framework\assertSame;

class BirthdayServiceTest extends TestCase
{
	use RefreshDatabase;

	#[Test]
	public function it_returns_same_instance_when_resolving_provider_object(): void
	{
		$dp = Mockery::mock(DataProvider::class);

		$service = new Birthday();

		assertSame($dp, $service->resolveDataProvider($dp));
	}

	#[Test]
	public function it_resolves_registered_provider_instance_by_alias(): void
	{
		$dp = Mockery::mock(DataProvider::class);

		$service = new Birthday();
		$service->enableDataProvider('mocked-data-provider', $dp);

		assertSame($dp, $service->resolveDataProvider('mocked-data-provider'));
	}

	#[Test]
	public function it_resolves_and_instantiates_provider_class_by_alias(): void
	{
		$dp = Mockery::mock(DataProvider::class, function (MockInterface $mock) {
			$mock->shouldReceive('make')->once()->andReturn($mock);
		});

		$service = new Birthday();
		$service->enableDataProvider('mocked-data-provider', $dp::class);

		assertSame($dp, $service->resolveDataProvider('mocked-data-provider'));
	}

	#[Test]
	public function it_creates_users_while_syncing(): void
	{
		$service = new Birthday();
		$service->makeSynchronizer(new BirthdayDataProvider())->sync();

		$this->assertDatabaseCount('users', 3);

		$this->assertDatabaseHas('users', [
			'name' => 'Иван Иванов',
			'birthdate' => '2025-05-20',
			'photo' => '/storage/ivanov.png',
			'post' => 'Директор'
		]);

		$this->assertDatabaseHas('users', [
			'name' => 'Арсений Петров',
			'birthdate' => '2024-09-30',
			'photo' => '/storage/petrov.png',
			'post' => 'Дизайнер'
		]);

		$this->assertDatabaseHas('users', [
			'name' => 'Михаил Сидоров',
			'birthdate' => '2024-12-31',
			'photo' => '/storage/sidorov.png',
			'post' => 'Уборщик'
		]);
	}

	#[Test]
	public function it_deletes_users_while_syncing(): void
	{
		$unidentified = User::factory()->create();

		$service = new Birthday();
		$service->makeSynchronizer(new BirthdayDataProvider())->sync();

		$this->assertDatabaseCount('users', 3);
		$this->assertDatabaseMissing('users', ['id' => $unidentified->id]);
	}

	#[Test]
	public function it_updates_users_while_syncing(): void
	{
		$dataProvider = new BirthdayDataProvider();
		$ivanov = $dataProvider->makeIvanov();
		$ivanov->birthdate = Carbon::createFromDate(month: 1, day: 15);

		User::create($ivanov->toArray());

		$service = new Birthday();
		$service->makeSynchronizer(new BirthdayDataProvider())->sync();

		$this->assertDatabaseCount('users', 3);

		$this->assertDatabaseHas('users', [
			'name' => 'Иван Иванов',
			'birthdate' => now()->setDate(2025, 5, 20)->toDateString()
		]);
	}
}
