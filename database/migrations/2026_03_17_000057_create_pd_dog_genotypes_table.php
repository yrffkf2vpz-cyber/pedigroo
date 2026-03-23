<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_genotypes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('genetic_test_id');
            $table->unsignedBigInteger('lab_id')->nullable();

            $table->string('genotype', 50); // pl. "N/N", "N/A", "A/A"
            $table->date('tested_at')->nullable();
            $table->string('certificate_url', 255)->nullable();

            $table->timestamps();

            $table->unique(['dog_id', 'genetic_test_id'], 'dog_genotype_unique');

            $table->foreign('dog_id')
                ->references('id')
                ->on('pd_dogs')
                ->onDelete('cascade');

            $table->foreign('genetic_test_id')
                ->references('id')
                ->on('pd_genetic_tests')
                ->onDelete('cascade');

            $table->foreign('lab_id')
                ->references('id')
                ->on('pd_genetic_labs')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_genotypes');
    }
};