<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_buyer_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');

            // kapcsolódó user
            $table->unsignedBigInteger('user_id')->unique();

            // minimális adatok
            $table->string('full_name', 255)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('phone', 50)->nullable();

            // státusz: pending / verified / blocked
            $table->string('status', 20)->default('pending');

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_buyer_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_buyer_profiles');
    }
};