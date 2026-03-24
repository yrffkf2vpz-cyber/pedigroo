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
            $table->unsignedBigInteger('working_event_id');
            $table->unsignedBigInteger('working_result_id');

            $table->timestamps();

            $table->unique(['dog_id', 'working_event_id']);
            $table->index('working_result_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_working_results');
    }
};
