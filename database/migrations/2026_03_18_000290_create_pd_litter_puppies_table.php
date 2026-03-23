<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_litter_puppies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('litter_id');

            $table->unsignedBigInteger('dog_id')->nullable(); // ha már van pd_dogs rekord

            $table->unsignedTinyInteger('birth_order')->nullable(); // hányadikként született

            $table->string('sex', 10)->nullable(); // 'male', 'female', vagy ország-specifikus kód
            $table->string('name_at_birth', 255)->nullable();
            $table->boolean('kept')->default(false); // tenyésztonél maradt-e

            $table->unsignedBigInteger('sold_country_id')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['litter_id'], 'idx_pd_litter_puppies_litter');
            $table->index(['dog_id'], 'idx_pd_litter_puppies_dog');
            $table->index(['sold_country_id'], 'idx_pd_litter_puppies_sold_country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_litter_puppies');
    }
};