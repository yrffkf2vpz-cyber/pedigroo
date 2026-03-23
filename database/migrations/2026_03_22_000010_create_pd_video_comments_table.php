<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_comments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // kommentelo user (community user is lehet)
            $table->unsignedBigInteger('user_id')->nullable();

            // melyik videóhoz tartozik
            $table->unsignedBigInteger('video_id');

            // komment szövege
            $table->text('comment_text');

            // státusz: pending / approved / rejected / hidden
            $table->string('status', 50)->default('approved');

            // metaadatok
            $table->string('ip_address', 50)->nullable();
            $table->string('device_fingerprint', 255)->nullable();

            $table->timestamps();

            $table->index(['video_id'], 'idx_pd_video_comments_video');
            $table->index(['user_id'], 'idx_pd_video_comments_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_comments');
    }
};