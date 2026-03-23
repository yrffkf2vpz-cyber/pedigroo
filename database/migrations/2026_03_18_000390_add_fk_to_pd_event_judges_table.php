<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_event_judges', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')->on('pd_events')
                ->cascadeOnDelete();

            $table->foreign('judge_id')
                ->references('id')->on('pd_judges')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_event_judges', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['judge_id']);
        });
    }
};