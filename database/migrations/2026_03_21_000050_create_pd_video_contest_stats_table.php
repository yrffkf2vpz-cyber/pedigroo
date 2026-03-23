<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_contest_stats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('contest_id');

            // összes nevezés
            $table->unsignedInteger('total_entries')->default(0);

            // összes szív a versenyben
            $table->unsignedInteger('total_hearts')->default(0);

            // összes unique szavazó
            $table->unsignedInteger('unique_voters')->default(0);

            // összes megtekintés
            $table->unsignedInteger('total_views')->default(0);

            // országok száma
            $table->unsignedInteger('unique_countries')->default(0);

            $table->timestamps();

            $table->index(['contest_id'], 'idx_pd_video_contest_stats_contest');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_contest_stats');
    }
};