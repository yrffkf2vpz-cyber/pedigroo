<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik felhasználó sessionje
            $table->unsignedBigInteger('user_id');

            // eszköz és böngészo adatok
            $table->string('device_name', 255)->nullable();      // pl. "iPhone 14"
            $table->string('device_type', 50)->nullable();       // pl. "mobile", "desktop"
            $table->string('browser', 100)->nullable();          // pl. "Chrome", "Safari"
            $table->string('platform', 100)->nullable();         // pl. "iOS", "Windows"

            // felismerési kulcs (nem auth token!)
            $table->string('session_key', 255)->unique();

            // IP és helyadatok
            $table->string('ip_address', 50)->nullable();
            $table->string('country_code', 10)->nullable();

            // session életciklusa
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_user_sessions_user');
            $table->index(['session_key'], 'idx_pd_user_sessions_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_sessions');
    }
};