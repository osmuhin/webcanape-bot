<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $telegram_user_id
 * @property string $birthdate
 * @property string $createdAt
 */
class User extends Model
{
	const UPDATED_AT = null;

	protected $fillable = ['first_name', 'last_name', 'telegram_user_id', 'birthdate'];

	protected $table = 'users';

	protected $casts = [
		'created_at' => 'timestamp',
		'birthdate' => 'date'
	];
}
