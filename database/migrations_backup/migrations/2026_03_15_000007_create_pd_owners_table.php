<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_owners', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Alapadatok
            $table->string('name', 255);
            $table->string('normalized_name', 255)->nullable();

            // Ország (opcionális, import miatt)
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('country', 10)->nullable();

            // Kennel kapcsolat (csak ha breeder)
            $table->unsignedBigInteger('kennel_id')->nullable();

            // Owner típusa
            $table->boolean('is_person')->default(true);
            
            // Import / fuzzy tisztítás
            $table->string('source', 50)->default('import');
            $table->string('fuzzy_key', 255)->nullable();

            $table->timestamps();

            // Indexek
            $table->index(['normalized_name']);
            $table->index(['fuzzy_key']);
            $table->index(['country_id']);
            $table->index(['kennel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_owners');
    }
};
