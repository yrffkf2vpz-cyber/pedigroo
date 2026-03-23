<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0); // 0–1000
            $table->string('level')->default('Bronze'); // Bronze, Silver, Gold, Platinum
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_scores');
    }
};
