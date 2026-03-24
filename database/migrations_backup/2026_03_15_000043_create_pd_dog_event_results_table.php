<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_event_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('event_result_id');

            $table->timestamps();

            $table->unique(['dog_id', 'event_id']);
            $table->index('event_result_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_event_results');
    }
};
