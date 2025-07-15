<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Birthday\BirthdayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Fixtures\BirthdayDataProvider;
use Tests\TestCase;

class SyncUsersTest extends TestCase
{
	// use RefreshDatabase;

	/**
	 * A basic feature test example.
	 */
	public function test_example(): void
	{
		$service = new BirthdayService(new BirthdayDataProvider());

		dd($service->sync());
	}
}
