<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_genetic_mutations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('genetic_test_id');

            $table->string('allele', 50); // pl. "A", "B", "C", "del", "ins"
            $table->string('genotype', 50); // pl. "A/A", "A/B", "B/B"
            $table->text('phenotype')->nullable();
            $table->string('risk_level', 50)->nullable(); // pl. "carrier", "affected"

            $table->timestamps();

            $table->unique(['genetic_test_id', 'genotype'], 'mutation_unique');

            $table->foreign('genetic_test_id')
                ->references('id')
                ->on('pd_genetic_tests')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_genetic_mutations');
    }
};