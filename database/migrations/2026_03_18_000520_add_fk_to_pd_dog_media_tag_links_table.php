<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_media_tag_links', function (Blueprint $table) {
            $table->foreign('tag_id')
                ->references('id')->on('pd_dog_media_tags')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_media_tag_links', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
        });
    }
};