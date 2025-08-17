<?php

namespace Database\Factories;

use App\Models\TelegramUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TelegramUserFactory>
 */
class TelegramUserFactory extends Factory
{
	protected $model = TelegramUser::class;

	public function definition(): array
	{
		return [
			'user_id' => User::factory(),
			'first_name' => fake()->firstName(),
			'last_name' => fake()->lastName(),
			'username' => fake()->userName(),
			'chat_id' => fake()->randomNumber(),
			'blocked' => fake()->boolean()
		];
	}
}
