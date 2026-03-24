<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_sport_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('sport_event_id');
            $table->unsignedBigInteger('sport_result_id');

            $table->timestamps();

            $table->unique(['dog_id', 'sport_event_id']);
            $table->index('sport_result_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_sport_results');
    }
};
