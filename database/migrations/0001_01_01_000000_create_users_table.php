<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('full_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('username')->unique();

            $table->string('password')->nullable();

            $table->string('phone_no')->unique();

            $table->string('otp')->nullable();

            $table->string('profile_image')->nullable();

            $table->string('api_token', 80)->nullable()->unique();

            $table->enum('account_type', ['women', 'child'])->default('women');

            $table->enum('account_status', ['verified', 'pending', 'blocked'])->default('pending');

            $table->enum('language', ['english', 'bangla'])->default('english');

            $table->boolean('is_premium_member')->default(0);

            $table->boolean('is_location_sharing')->default(0);

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
