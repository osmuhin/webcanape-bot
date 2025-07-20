<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $photo
 * @property string $post
 * @property \Carbon\Carbon $birthdate
 * @property string|null $telegram_user_id
 * @property bool $telegram_allow_notifications
 * @property string $joined_at
 */
class User extends Model
{
	use HasFactory;

	public const UPDATED_AT = null;

	protected $fillable = ['first_name', 'last_name', 'photo', 'post', 'telegram_user_id', 'birthdate'];

	protected $table = 'users';

	protected $casts = [
		'birthdate' => 'date'
	];

	public function telegramUser(): HasOne
	{
		return $this->hasOne(TelegramUser::class, 'user_id');
	}
}
