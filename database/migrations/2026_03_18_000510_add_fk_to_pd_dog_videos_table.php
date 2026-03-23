<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->foreign('dog_id')
                ->references('id')->on('pd_dogs')
                ->cascadeOnDelete();

            $table->foreign('media_id')
                ->references('id')->on('media')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->dropForeign(['dog_id']);
            $table->dropForeign(['media_id']);
        });
    }
};