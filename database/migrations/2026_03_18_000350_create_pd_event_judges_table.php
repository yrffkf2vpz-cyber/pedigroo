<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_event_judges', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('judge_id');

            $table->timestamps();

            $table->index(['event_id'], 'idx_pd_event_judges_event');
            $table->index(['judge_id'], 'idx_pd_event_judges_judge');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_event_judges');
    }
};