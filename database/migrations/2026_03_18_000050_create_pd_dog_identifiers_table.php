<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_identifiers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('type', 100); // microchip, tattoo, dna_profile, etc.
            $table->string('value', 255);

            $table->date('issued_at')->nullable();
            $table->string('issuer', 255)->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_identifiers_dog');
            $table->index(['type'], 'idx_pd_dog_identifiers_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_identifiers');
    }
};