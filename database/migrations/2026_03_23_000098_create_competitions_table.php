<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();

            // Kapcsolat a kategóriához
            $table->foreignId('category_id')
                ->constrained('competition_categories')
                ->cascadeOnDelete();

            // Alap adatok
            $table->string('title');
            $table->text('description')->nullable();

            // Idozítés
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // Verseny állapota
            $table->enum('status', ['upcoming', 'active', 'finished'])
                ->default('upcoming');

            // Automatikusan generált-e (AI / SystemScanner)
            $table->boolean('is_auto_generated')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
