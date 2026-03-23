<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_family_access_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki nézte meg
            $table->unsignedBigInteger('user_id');

            // melyik kutya / család / pedigré
            $table->unsignedBigInteger('dog_id')->nullable();
            $table->unsignedBigInteger('family_id')->nullable();

            // hozzáférés oka: interest / inquiry / recommendation
            $table->string('reason', 50)->nullable();

            // mikor történt
            $table->timestamp('accessed_at')->nullable();

            // metaadatok (IP, device, session)
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_family_access_user');
            $table->index(['dog_id'], 'idx_pd_family_access_dog');
            $table->index(['family_id'], 'idx_pd_family_access_family');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_family_access_log');
    }
};