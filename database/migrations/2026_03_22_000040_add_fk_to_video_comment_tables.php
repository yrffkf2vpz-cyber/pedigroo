<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_video_comments', function (Blueprint $table) {
            $table->foreign('user_id', 'fk_pd_video_comments_user')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('video_id', 'fk_pd_video_comments_video')
                ->references('id')->on('pd_video_media')
                ->cascadeOnDelete();
        });

        Schema::table('pd_video_comment_flags', function (Blueprint $table) {
            $table->foreign('comment_id', 'fk_pd_comment_flags_comment')
                ->references('id')->on('pd_video_comments')
                ->cascadeOnDelete();
        });

        Schema::table('pd_video_comment_moderation', function (Blueprint $table) {
            $table->foreign('comment_id', 'fk_pd_comment_moderation_comment')
                ->references('id')->on('pd_video_comments')
                ->cascadeOnDelete();

            $table->foreign('moderator_id', 'fk_pd_comment_moderation_moderator')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_video_comments', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_comments_user');
            $table->dropForeign('fk_pd_video_comments_video');
        });

        Schema::table('pd_video_comment_flags', function (Blueprint $table) {
            $table->dropForeign('fk_pd_comment_flags_comment');
        });

        Schema::table('pd_video_comment_moderation', function (Blueprint $table) {
            $table->dropForeign('fk_pd_comment_moderation_comment');
            $table->dropForeign('fk_pd_comment_moderation_moderator');
        });
    }
};