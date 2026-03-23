<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_show_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('judge_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();

            $table->string('grade', 50)->nullable();       // pl. Excellent, Very Good
            $table->string('placement', 50)->nullable();   // pl. 1, 2, 3, 4
            $table->string('title_awarded', 100)->nullable(); // pl. CAC, CACIB, BOB

            $table->boolean('is_best_of_breed')->default(false);
            $table->boolean('is_best_of_sex')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_show_dog');
            $table->index(['event_id'], 'idx_pd_show_event');
            $table->index(['judge_id'], 'idx_pd_show_judge');
            $table->index(['class_id'], 'idx_pd_show_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_show_results');
    }
};