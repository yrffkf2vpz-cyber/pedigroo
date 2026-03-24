<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_breeders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255)->nullable();
            $table->string('country', 10)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['name']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_breeders');
    }
};
