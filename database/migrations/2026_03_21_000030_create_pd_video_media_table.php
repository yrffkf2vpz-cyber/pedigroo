<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_media', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik nevezéshez tartozik
            $table->unsignedBigInteger('entry_id');

            // videó URL vagy storage path
            $table->string('video_url', 500);

            // thumbnail
            $table->string('thumbnail_url', 500)->nullable();

            // metaadatok
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('file_size_kb')->nullable();

            $table->timestamps();

            $table->index(['entry_id'], 'idx_pd_video_media_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_media');
    }
};