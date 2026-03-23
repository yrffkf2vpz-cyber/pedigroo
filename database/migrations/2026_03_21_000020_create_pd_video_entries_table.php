<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_entries', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik versenyre neveztek
            $table->unsignedBigInteger('contest_id');

            // nevezo kennel
            $table->unsignedBigInteger('kennel_id')->nullable();

            // nevezett kutya (opcionális, lehet kennel promo is)
            $table->unsignedBigInteger('dog_id')->nullable();

            // nevezés státusza
            $table->string('status', 50)->default('pending'); 
            // pending / approved / rejected

            $table->timestamps();

            $table->index(['contest_id'], 'idx_pd_video_entries_contest');
            $table->index(['kennel_id'], 'idx_pd_video_entries_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_entries');
    }
};