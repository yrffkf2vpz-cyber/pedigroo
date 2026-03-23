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

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('sire_id')->nullable();
            $table->unsignedBigInteger('dam_id')->nullable();

            $table->timestamps();

            $table->unique(['dog_id'], 'uq_pd_parents_dog');

            $table->index(['sire_id'], 'idx_pd_parents_sire');
            $table->index(['dam_id'], 'idx_pd_parents_dam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_parents');
    }
};