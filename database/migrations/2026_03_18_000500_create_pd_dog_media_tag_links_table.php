<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_media_tag_links', function (Blueprint $table) {
            $table->bigIncrements('id');

            // polymorphic: lehet kép vagy videó is
            $table->unsignedBigInteger('media_id');
            $table->string('media_type', 50); // 'image' vagy 'video'

            $table->unsignedBigInteger('tag_id');

            $table->timestamps();

            $table->index(['media_id', 'media_type'], 'idx_pd_media_tag_links_media');
            $table->index(['tag_id'], 'idx_pd_media_tag_links_tag');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_media_tag_links');
    }
};