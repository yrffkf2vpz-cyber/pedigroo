<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_color_acceptance', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Kapcsolat a fajta színhez (master tábla)
            $table->unsignedBigInteger('breed_color_id');

            // Kapcsolat a hatósághoz (FCI, AKC, KC, UKC, CKC, ANKC, stb.)
            $table->unsignedBigInteger('authority_id');

            // Elfogadási státusz (accepted, disallowed, restricted, experimental, stb.)
            $table->string('status', 50);

            // Idobeli érvényesség
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexek
            $table->index(['breed_color_id', 'authority_id']);

            // Foreign key-ek
            $table->foreign('breed_color_id')
                ->references('id')
                ->on('pd_breed_colors')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_color_acceptance');
    }
};