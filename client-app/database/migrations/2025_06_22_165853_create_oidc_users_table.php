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
        Schema::create('oidc_users', function (Blueprint $table) {
            $table->string('sub')->primary(); // OIDC の subject（一意なユーザーID）
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oidc_users');
    }
};
