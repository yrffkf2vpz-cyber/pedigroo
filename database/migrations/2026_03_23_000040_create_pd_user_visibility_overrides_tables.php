<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_visibility_overrides', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');     // kinek adja az engedélyt
            $table->unsignedBigInteger('kennel_id');   // melyik kennelhez

            $table->json('allowed_fields'); 
            // pl.: ["pedigree", "health", "litter_info"]

            $table->timestamps();

            // Indexek
            $table->index('user_id');
            $table->index('kennel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_visibility_overrides');
    }
};