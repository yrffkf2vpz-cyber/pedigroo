<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('type', 50); // image, video
            $table->string('file_path', 500);

            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_media_dog');
            $table->index(['type'], 'idx_pd_dog_media_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_media');
    }
};