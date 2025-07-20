<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('photo')->nullable();
			$table->string('post');
			$table->date('birthdate')->index();
			$table->string('checksum', 32)->collation('ascii_bin')->index();
			$table->timestamp('created_at')->useCurrent();
		});

		Schema::create('telegram_users', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('username')->unique();
			$table->string('chat_id')->unique();
			$table->boolean('blocked')->default(false);
			$table->timestamp('created_at')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('telegram_users');
		Schema::dropIfExists('users');
	}
};
