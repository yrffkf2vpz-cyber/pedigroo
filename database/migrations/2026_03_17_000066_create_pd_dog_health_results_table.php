<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_health_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('screening_id');

            $table->string('result', 255); // pl. "HD-A", "Clear", "Normal"
            $table->date('tested_at')->nullable();
            $table->string('certificate_url', 255)->nullable();

            $table->timestamps();

            $table->unique(['dog_id', 'screening_id'], 'dog_health_result_unique');

            $table->foreign('dog_id')
                ->references('id')
                ->on('pd_dogs')
                ->onDelete('cascade');

            $table->foreign('screening_id')
                ->references('id')
                ->on('pd_health_screenings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_health_results');
    }
};