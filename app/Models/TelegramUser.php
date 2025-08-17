<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $chat_id
 * @property bool $blocked
 * @property \Carbon\Carbon $created_at
 */
class TelegramUser extends Model
{
	public const UPDATED_AT = null;

	protected $guarded = ['id', 'created_at', 'user_id'];

	protected $table = 'telegram_users';

	protected $casts = [
		'birthdate' => 'date'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
