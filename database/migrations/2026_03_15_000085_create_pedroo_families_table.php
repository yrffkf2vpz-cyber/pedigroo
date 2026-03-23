<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_families', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('dog_name', 255)->nullable();
            $table->string('related_dog_name', 255)->nullable();

            // pl. "sibling", "offspring", "parent", "half-sibling", stb.
            $table->string('relation_type', 50)->nullable();

            // HELYES datetime mezok
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['dog_name']);
            $table->index(['related_dog_name']);
            $table->index(['relation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_families');
    }
};
