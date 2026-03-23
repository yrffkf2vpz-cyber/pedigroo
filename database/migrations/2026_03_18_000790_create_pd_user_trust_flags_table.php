<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_trust_flags', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik user
            $table->unsignedBigInteger('user_id')->unique();

            // zöld / sárga / piros
            $table->string('trust_level', 20)->default('green');

            // figyelmeztetések száma
            $table->unsignedInteger('warnings')->default(0);

            // utolsó figyelmeztetés ideje
            $table->timestamp('last_warning_at')->nullable();

            // metaadatok (pl. miért kapta)
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_trust_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_trust_flags');
    }
};