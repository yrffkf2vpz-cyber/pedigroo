<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_videos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('media_id')->nullable(); // kapcsolódik a media táblához

            $table->string('source', 50)->default('upload'); // upload, youtube, tiktok, url
            $table->string('url', 500)->nullable();          // külso videó link
            $table->string('thumbnail_url', 500)->nullable();

            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('resolution', 50)->nullable();    // pl. 1080p, 4K

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_videos_dog');
            $table->index(['media_id'], 'idx_pd_dog_videos_media');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_videos');
    }
};