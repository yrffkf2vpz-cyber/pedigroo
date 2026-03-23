<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_performance_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id')->nullable();

            $table->string('sport', 100)->nullable();   // pl. Agility, Flyball, Coursing
            $table->string('result', 100)->nullable();  // pl. idoeredmény, pontszám
            $table->string('placement', 50)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_perf_dog');
            $table->index(['event_id'], 'idx_pd_perf_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_performance_results');
    }
};