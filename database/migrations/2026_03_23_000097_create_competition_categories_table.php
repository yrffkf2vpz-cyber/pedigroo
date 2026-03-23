<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_categories', function (Blueprint $table) {
            $table->id();

            // Alap adatok
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Milyen típusú tartalom kell hozzá?
            $table->enum('media_type', ['image', 'video', 'both'])->default('both');

            // Milyen verseny típus?
            $table->enum('category_type', [
                'beauty',        // szépségverseny
                'funny',         // vicces
                'sport',         // sport / mozgás
                'working',       // munkakutya
                'hunting',       // vadászkutya
                'show',          // online kiállítás
                'costume',       // jelmez
                'trending',      // AI által generált trending kategória
                'custom',        // admin által létrehozott
            ])->default('custom');

            // AI generálhat-e belole automatikus versenyt?
            $table->boolean('auto_generate')->default(false);

            // AI milyen gyakran generáljon belole versenyt?
            $table->enum('generate_frequency', [
                'none',
                'daily',
                'weekly',
                'monthly',
                'seasonal',
                'yearly',
                'trending',
            ])->default('none');

            // AI súlyozás (melyik kategóriát preferálja)
            $table->unsignedInteger('ai_weight')->default(1);

            // Aktív-e a kategória?
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_categories');
    }
};
