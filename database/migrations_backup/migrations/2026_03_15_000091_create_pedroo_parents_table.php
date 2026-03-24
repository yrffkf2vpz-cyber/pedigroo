<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_parents', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Gyerek neve (nyers)
            $table->string('child_name', 255)->nullable();

            // Szülo neve (nyers)
            $table->string('parent_name', 255)->nullable();

            // Kapcsolat típusa: sire = apa, dam = anya
            $table->enum('relation', ['sire', 'dam'])->nullable();

            // HELYES datetime mezok
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['child_name']);
            $table->index(['parent_name']);
            $table->index(['relation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_parents');
    }
};
