<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_votes', function (Blueprint $table) {
            $table->bigIncrements('id');

            // szavazó user – lehet community user is
            $table->unsignedBigInteger('user_id')->nullable();

            // melyik videóra szavazott
            $table->unsignedBigInteger('video_id');

            // csalás elleni meta
            $table->string('device_fingerprint', 255)->nullable();
            $table->string('ip_address', 50)->nullable();

            $table->timestamps();

            // 1 user = 1 szavazat / videó
            $table->unique(['user_id', 'video_id'], 'uniq_pd_video_vote_user_video');

            $table->index(['video_id'], 'idx_pd_video_votes_video');
            $table->index(['ip_address'], 'idx_pd_video_votes_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_votes');
    }
};
