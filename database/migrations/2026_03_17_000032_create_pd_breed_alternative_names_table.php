<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_alternative_names', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('authority_id')->nullable();

            $table->string('name', 255);
            $table->string('language', 10)->nullable(); // ISO kód pl. "en", "de", "hu"

            $table->timestamps();

            $table->unique(['breed_id', 'name', 'authority_id'], 'alt_name_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_alternative_names');
    }
};