<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_health', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('test_type', 100); // HD, ED, DM, PRA, etc.
            $table->string('result', 100)->nullable();
            $table->date('tested_at')->nullable();
            $table->string('laboratory', 255)->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_health_dog');
            $table->index(['test_type'], 'idx_pd_dog_health_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_health');
    }
};