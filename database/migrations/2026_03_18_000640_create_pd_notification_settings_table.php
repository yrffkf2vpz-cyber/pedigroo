<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_notification_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik felhasználó beállításai
            $table->unsignedBigInteger('user_id')->unique();

            // beállítások JSON-ben (pl. {"new_litter": true, "favorites": false})
            $table->json('settings')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_notif_settings_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_notification_settings');
    }
};