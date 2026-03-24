<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_club_timeline', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('club_id');
            $table->string('event_type', 100);
            $table->timestamp('timestamp')->nullable();
            $table->json('data')->nullable();

            $table->timestamps();

            $table->index('club_id');
            $table->index('event_type');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_club_timeline');
    }
};