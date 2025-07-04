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
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // ユーザー識別用
            $table->string('access_token', 1000);
            $table->string('refresh_token', 1000)->nullable();
            $table->string('token_type')->default('Bearer');
            $table->dateTime('expires_at'); // 秒数
            $table->timestamp('fetched_at')->useCurrent(); // 取得日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
