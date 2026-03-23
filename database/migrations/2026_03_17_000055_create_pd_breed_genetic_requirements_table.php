<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_genetic_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('genetic_test_id');
            $table->unsignedBigInteger('authority_id')->nullable();

            $table->string('required_genotype', 50)->nullable(); // pl. "N/N"
            $table->string('recommendation_level', 50)->nullable(); // pl. "mandatory", "recommended"

            $table->timestamps();

            $table->unique(['breed_id', 'genetic_test_id', 'authority_id'], 'breed_test_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('genetic_test_id')
                ->references('id')
                ->on('pd_genetic_tests')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_genetic_requirements');
    }
};