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
			$table->string('birthdate', 5)->collation('ascii_bin')->index();
			$table->string('telegram_user_id')->unique()->nullable();
			$table->boolean('telegram_allow_notifications')->default(false);
			$table->string('checksum', 32)->collation('ascii_bin')->index()->nullable();
			$table->timestamp('joined_at')->nullable()->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};
