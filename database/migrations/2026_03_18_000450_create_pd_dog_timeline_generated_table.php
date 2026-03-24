<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_timeline_generated', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ugyanaz a polymorphic rendszer
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type', 100);

            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->date('occurred_at')->nullable();

            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['entity_id', 'entity_type'], 'idx_pd_timeline_gen_entity');
            $table->index(['event_type_id'], 'idx_pd_timeline_gen_event_type');
            $table->index(['occurred_at'], 'idx_pd_timeline_gen_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_timeline_generated');
    }
};