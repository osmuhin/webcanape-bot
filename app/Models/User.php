<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $photo
 * @property string $post
 * @property string $birthdate
 * @property string|null $telegram_user_id
 * @property string $joined_at
 */
class User extends Model
{
	const UPDATED_AT = null;

	protected $fillable = ['first_name', 'last_name', 'telegram_user_id', 'birthdate'];

	protected $table = 'users';

	protected $casts = [
		'joined_at' => 'timestamp',
		'birthdate' => 'date'
	];
}
