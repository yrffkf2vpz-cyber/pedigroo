<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik felhasználóhoz tartozik
            $table->unsignedBigInteger('user_id')->unique();

            // preferenciák JSON-ben
            // pl. {"language": "hu", "theme": "dark", "default_country": "HU"}
            $table->json('preferences')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_user_pref_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_preferences');
    }
};