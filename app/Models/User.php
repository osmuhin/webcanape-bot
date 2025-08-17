<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property string $photo
 * @property string $post
 * @property \Carbon\Carbon $birthdate
 * @property string $checksum
 * @property \Carbon\Carbon $created_at
 */
class User extends Model
{
	use HasFactory;

	public const UPDATED_AT = null;

	protected $guarded = ['id', 'created_at'];

	protected $table = 'users';

	protected $casts = [
		'birthdate' => 'date'
	];

	public function telegramUser(): HasOne
	{
		return $this->hasOne(TelegramUser::class, 'user_id');
	}
}
