<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Birthday\BirthdayService;
use App\Services\Birthday\UserRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\BirthdayDataProvider;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

class SyncUsersTest extends TestCase
{
	use RefreshDatabase;

	public function test_create_users(): void
	{
		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();

		$this->assertDatabaseCount('users', 3);

		$this->assertDatabaseHas('users', [
			'first_name' => 'Иван',
			'last_name' => 'Иванов',
			'birthdate' => '2025-05-20',
			'photo' => '/storage/ivanov.png',
			'post' => 'Директор'
		]);

		$this->assertDatabaseHas('users', [
			'first_name' => 'Арсений',
			'last_name' => 'Петров',
			'birthdate' => '2024-09-30',
			'photo' => '/storage/petrov.png',
			'post' => 'Дизайнер'
		]);

		$this->assertDatabaseHas('users', [
			'first_name' => 'Михаил',
			'last_name' => 'Сидоров',
			'birthdate' => '2024-12-31',
			'photo' => '/storage/sidorov.png',
			'post' => 'Уборщик'
		]);
	}

	public function test_delete_users(): void
	{
		$unidentified = User::factory()->create();

		$service = new BirthdayService(new BirthdayDataProvider());
		$service->sync();

		$this->assertDatabaseCount('users', 3);
		$this->assertDatabaseMissing('users', ['id' => $unidentified->id]);
	}

	public function test_update_users(): void
	{
		$dataProvider = new BirthdayDataProvider();
		$ivanov = $dataProvider->makeIvanov();
		$ivanov->birthdate = Carbon::createFromDate(month: 1, day: 15);

		assertNotSame($ivanov->checksum(), $dataProvider->makeIvanov()->checksum());

		UserRepository::create($ivanov);

		$service = new BirthdayService($dataProvider);
		$service->sync();

		$this->assertDatabaseCount('users', 3);

		$updatedIvanov = UserRepository::fetchByName('Иван', 'Иванов');

		assertSame('20-05', $updatedIvanov->birthdate->format('d-m'));
	}
}
