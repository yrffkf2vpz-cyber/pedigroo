<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_rings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('number', 50);
            $table->string('label', 255)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_rings');
    }
};
