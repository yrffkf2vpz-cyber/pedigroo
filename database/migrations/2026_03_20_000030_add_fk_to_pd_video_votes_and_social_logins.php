<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_video_votes', function (Blueprint $table) {
            // user_id opcionális, ezért onDelete: set null
            $table->foreign('user_id', 'fk_pd_video_votes_user')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        Schema::table('pd_social_logins', function (Blueprint $table) {
            $table->foreign('user_id', 'fk_pd_social_logins_user')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_video_votes', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_votes_user');
        });

        Schema::table('pd_social_logins', function (Blueprint $table) {
            $table->dropForeign('fk_pd_social_logins_user');
        });
    }
};