<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_family_cache', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->json('family_tree')->nullable();
            $table->unsignedInteger('generations')->default(5);

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_family_cache_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_family_cache');
    }
};