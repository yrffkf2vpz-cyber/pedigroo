<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_behavior_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('behavior_test_id');

            $table->string('score', 50)->nullable();      // pl. 85/100
            $table->string('rating', 50)->nullable();     // pl. Passed, Excellent
            $table->text('details')->nullable();          // részletes leírás

            $table->timestamps();

            $table->index(['behavior_test_id'], 'idx_pd_behavior_results_test');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_behavior_results');
    }
};