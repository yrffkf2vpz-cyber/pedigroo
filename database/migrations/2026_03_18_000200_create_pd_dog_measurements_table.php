<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_measurements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->float('height_cm')->nullable();
            $table->float('weight_kg')->nullable();
            $table->float('chest_cm')->nullable();
            $table->float('length_cm')->nullable();

            $table->date('measured_at')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_measurements_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_measurements');
    }
};