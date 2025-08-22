<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Birthday\BirthdayService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\BirthdayDataProvider;
use Tests\TestCase;

class BirthdaySynchronizerTest extends TestCase
{
	use RefreshDatabase;

	#[Test]
	public function it_creates_users_while_syncing(): void
	{
		$service = new BirthdayService();
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

		$service = new BirthdayService();
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

		$service = new BirthdayService();
		$service->makeSynchronizer(new BirthdayDataProvider())->sync();

		$this->assertDatabaseCount('users', 3);

		$this->assertDatabaseHas('users', [
			'name' => 'Иван Иванов',
			'birthdate' => now()->setDate(2025, 5, 20)->toDateString()
		]);
	}
}
