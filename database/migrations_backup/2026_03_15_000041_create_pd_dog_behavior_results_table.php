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

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('behavior_test_id');
            $table->unsignedBigInteger('behavior_result_id');

            $table->timestamps();

            $table->index(['dog_id', 'behavior_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_behavior_results');
    }
};
