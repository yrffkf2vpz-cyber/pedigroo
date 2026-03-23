<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('event_type', 100);
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_activity_dog');
            $table->index(['event_type'], 'idx_pd_dog_activity_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_activity_log');
    }
};