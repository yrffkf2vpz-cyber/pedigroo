<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_protocols', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('screening_id');

            $table->string('step_title', 255);
            $table->text('step_description')->nullable();
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->foreign('screening_id')
                ->references('id')
                ->on('pd_health_screenings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_protocols');
    }
};