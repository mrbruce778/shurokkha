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
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
