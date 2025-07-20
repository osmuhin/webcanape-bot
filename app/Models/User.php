<?php

namespace App\Models;

use App\Casts\AsBirthdate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $photo
 * @property string $post
 * @property \Carbon\Carbon $birthdate
 * @property string|null $telegram_user_id
 * @property string $joined_at
 */
class User extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = ['first_name', 'last_name', 'photo', 'post', 'telegram_user_id', 'birthdate'];

	protected $table = 'users';

	protected $casts = [
		'joined_at' => 'timestamp',
		'birthdate' => AsBirthdate::class
	];
}
