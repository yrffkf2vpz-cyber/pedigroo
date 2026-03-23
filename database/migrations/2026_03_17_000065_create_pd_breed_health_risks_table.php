<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_health_risks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('condition_id');

            $table->string('risk_level', 50); // pl. "low", "medium", "high"
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['breed_id', 'condition_id'], 'breed_health_risk_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('condition_id')
                ->references('id')
                ->on('pd_health_conditions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_health_risks');
    }
};