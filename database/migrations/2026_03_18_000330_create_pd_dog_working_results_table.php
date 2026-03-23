<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_working_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('judge_id')->nullable();

            $table->string('discipline', 100)->nullable(); // pl. IGP, Obedience, Tracking
            $table->string('score', 50)->nullable();        // pl. 96/100
            $table->string('rating', 50)->nullable();       // pl. V, SG, G

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_working_dog');
            $table->index(['event_id'], 'idx_pd_working_event');
            $table->index(['judge_id'], 'idx_pd_working_judge');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_working_results');
    }
};