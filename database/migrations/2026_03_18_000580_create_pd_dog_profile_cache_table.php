<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_profile_cache', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id')->unique();

            // elore generált JSON profil
            $table->json('profile_json')->nullable();

            // gyors frissítéshez
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_profile_cache_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_profile_cache');
    }
};