<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_stats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            // Breeding stats
            $table->integer('offspring_count')->default(0);
            $table->integer('litters_sired')->default(0);
            $table->integer('litters_whelped')->default(0);

            // Titles & achievements
            $table->integer('titles_count')->default(0);
            $table->integer('show_results_count')->default(0);
            $table->integer('working_results_count')->default(0);
            $table->integer('performance_results_count')->default(0);

            // Health
            $table->integer('health_tests_count')->default(0);

            // Media & documents
            $table->integer('media_count')->default(0);
            $table->integer('documents_count')->default(0);

            // Activity log
            $table->integer('activity_events_count')->default(0);

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_stats_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_stats');
    }
};