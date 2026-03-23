<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('contest_id');
            $table->unsignedBigInteger('entry_id');

            // helyezés: 1, 2, 3, stb.
            $table->unsignedInteger('placement')->nullable();

            // összes szív
            $table->unsignedInteger('total_hearts')->default(0);

            // unique szavazók
            $table->unsignedInteger('unique_voters')->default(0);

            $table->timestamps();

            $table->index(['contest_id'], 'idx_pd_video_results_contest');
            $table->index(['entry_id'], 'idx_pd_video_results_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_results');
    }
};