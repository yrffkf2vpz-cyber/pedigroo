<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_trust_score', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->integer('score')->default(0); 
            // 0–100 skála

            $table->enum('level', ['green', 'yellow', 'red'])
                  ->default('green');

            $table->timestamp('last_update')->nullable();

            // Indexek
            $table->index('user_id');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_trust_score');
    }
};