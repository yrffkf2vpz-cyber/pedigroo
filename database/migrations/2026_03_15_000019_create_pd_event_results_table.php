<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_event_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id');

            $table->unsignedBigInteger('event_class_id')->nullable();
            $table->unsignedBigInteger('placement_id')->nullable();
            $table->unsignedBigInteger('qualification_id')->nullable();

            $table->unsignedBigInteger('judge_id')->nullable();
            $table->unsignedBigInteger('ring_id')->nullable();

            $table->timestamps();

            // Core relations
            $table->foreign('dog_id')
                ->references('id')
                ->on('pd_dogs')
                ->onDelete('cascade');

            $table->foreign('event_id')
                ->references('id')
                ->on('pd_events')
                ->onDelete('cascade');

            // Corrected domain relations
            $table->foreign('event_class_id')
                ->references('id')
                ->on('event_types')
                ->onDelete('set null');

            $table->foreign('placement_id')
                ->references('id')
                ->on('pd_placements')
                ->onDelete('set null');

            $table->foreign('qualification_id')
                ->references('id')
                ->on('pd_qualifications')
                ->onDelete('set null');

            $table->foreign('judge_id')
                ->references('id')
                ->on('pd_judges')
                ->onDelete('set null');

            $table->foreign('ring_id')
                ->references('id')
                ->on('pd_rings')
                ->onDelete('set null');

            $table->unique(['dog_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_event_results');
    }
};
