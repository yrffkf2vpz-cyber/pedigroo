<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_children', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Mindkét szülo neve – akár üresen is
            $table->string('sire_name', 255)->nullable(); // apa
            $table->string('dam_name', 255)->nullable();  // anya

            // Gyerek neve
            $table->string('child_name', 255)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['sire_name']);
            $table->index(['dam_name']);
            $table->index(['child_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_children');
    }
};
