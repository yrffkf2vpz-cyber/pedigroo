<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_video_entries', function (Blueprint $table) {
            $table->foreign('contest_id', 'fk_pd_video_entries_contest')
                ->references('id')->on('pd_video_contests')
                ->cascadeOnDelete();

            $table->foreign('kennel_id', 'fk_pd_video_entries_kennel')
                ->references('id')->on('pd_kennels')
                ->nullOnDelete();

            $table->foreign('dog_id', 'fk_pd_video_entries_dog')
                ->references('id')->on('pd_dogs')
                ->nullOnDelete();
        });

        Schema::table('pd_video_media', function (Blueprint $table) {
            $table->foreign('entry_id', 'fk_pd_video_media_entry')
                ->references('id')->on('pd_video_entries')
                ->cascadeOnDelete();
        });

        Schema::table('pd_video_results', function (Blueprint $table) {
            $table->foreign('contest_id', 'fk_pd_video_results_contest')
                ->references('id')->on('pd_video_contests')
                ->cascadeOnDelete();

            $table->foreign('entry_id', 'fk_pd_video_results_entry')
                ->references('id')->on('pd_video_entries')
                ->cascadeOnDelete();
        });

        Schema::table('pd_video_contest_stats', function (Blueprint $table) {
            $table->foreign('contest_id', 'fk_pd_video_contest_stats_contest')
                ->references('id')->on('pd_video_contests')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_video_entries', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_entries_contest');
            $table->dropForeign('fk_pd_video_entries_kennel');
            $table->dropForeign('fk_pd_video_entries_dog');
        });

        Schema::table('pd_video_media', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_media_entry');
        });

        Schema::table('pd_video_results', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_results_contest');
            $table->dropForeign('fk_pd_video_results_entry');
        });

        Schema::table('pd_video_contest_stats', function (Blueprint $table) {
            $table->dropForeign('fk_pd_video_contest_stats_contest');
        });
    }
};