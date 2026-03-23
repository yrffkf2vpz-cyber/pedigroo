<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_genetic_risks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('genetic_test_id');

            $table->string('risk_level', 50); // pl. "low", "medium", "high"
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['breed_id', 'genetic_test_id'], 'breed_risk_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('genetic_test_id')
                ->references('id')
                ->on('pd_genetic_tests')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_genetic_risks');
    }
};