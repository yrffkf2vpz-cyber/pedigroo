<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_comment_moderation', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('comment_id');

            // moderator user
            $table->unsignedBigInteger('moderator_id');

            // approved / rejected / hidden / banned_user
            $table->string('action', 50);

            $table->text('reason')->nullable();

            $table->timestamps();

            $table->index(['comment_id'], 'idx_pd_comment_moderation_comment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_comment_moderation');
    }
};