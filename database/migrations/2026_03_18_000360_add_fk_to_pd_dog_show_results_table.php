<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_show_results', function (Blueprint $table) {
            $table->foreign('dog_id')
                ->references('id')->on('pd_dogs')
                ->cascadeOnDelete();

            $table->foreign('event_id')
                ->references('id')->on('pd_events')
                ->nullOnDelete();

            $table->foreign('judge_id')
                ->references('id')->on('pd_judges')
                ->nullOnDelete();

            $table->foreign('class_id')
                ->references('id')->on('pd_classes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_show_results', function (Blueprint $table) {
            $table->dropForeign(['dog_id']);
            $table->dropForeign(['event_id']);
            $table->dropForeign(['judge_id']);
            $table->dropForeign(['class_id']);
        });
    }
};