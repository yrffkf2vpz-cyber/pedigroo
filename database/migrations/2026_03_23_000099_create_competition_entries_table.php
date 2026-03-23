<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_entries', function (Blueprint $table) {
            $table->id();

            // Kapcsolatok
            $table->foreignId('competition_id')
                ->constrained('competitions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Média adatok
            $table->enum('media_type', ['image', 'video']);
            $table->string('media_url');

            // Opcionális felirat
            $table->string('caption')->nullable();

            // Szavazatok száma
            $table->unsignedInteger('votes_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_entries');
    }
};
