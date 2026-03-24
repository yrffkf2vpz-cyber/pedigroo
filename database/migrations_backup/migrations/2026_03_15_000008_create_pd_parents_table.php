<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_parents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');     // a gyerek
            $table->unsignedBigInteger('parent_id');  // a szülo

            $table->enum('relation', ['sire', 'dam']); // apa vagy anya

            $table->timestamps();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['parent_id']);
            $table->index(['relation']);

            // Kapcsolatok
            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_parents');
    }
};
