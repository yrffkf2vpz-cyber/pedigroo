<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_contests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title', 255);
            $table->text('description')->nullable();

            // weekly / monthly / special
            $table->string('contest_type', 50)->default('weekly');

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // active / closed / archived
            $table->string('status', 50)->default('active');

            $table->timestamps();

            $table->index(['status'], 'idx_pd_video_contests_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_contests');
    }
};