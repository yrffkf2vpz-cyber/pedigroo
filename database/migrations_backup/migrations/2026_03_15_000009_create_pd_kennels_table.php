<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_kennels', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);

            $table->string('country', 10)->nullable();

            $table->boolean('needs_review')->default(false);

            $table->unsignedBigInteger('owner_id')->nullable();

            $table->timestamps();

            // owner_id = kennel tulajdonos / tenyészto
            // szerepkülönbség nem itt, hanem kontextusban jelenik meg
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_kennels');
    }
};
