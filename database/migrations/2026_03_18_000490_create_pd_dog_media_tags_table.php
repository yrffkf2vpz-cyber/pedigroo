<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_media_tags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 100);
            $table->string('slug', 120)->unique();

            $table->timestamps();

            $table->index(['slug'], 'idx_pd_media_tags_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_media_tags');
    }
};