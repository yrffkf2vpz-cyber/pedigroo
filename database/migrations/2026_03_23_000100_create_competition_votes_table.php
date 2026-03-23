<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_votes', function (Blueprint $table) {
            $table->id();

            // Kapcsolatok
            $table->foreignId('entry_id')
                ->constrained('competition_entries')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Egy user csak egyszer szavazhat egy nevezésre
            $table->unique(['entry_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_votes');
    }
};
