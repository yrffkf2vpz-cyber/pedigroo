<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable()->after('media_id');
            $table->unsignedBigInteger('owner_id')->nullable()->after('uploaded_by_user_id');

            $table->index(['uploaded_by_user_id'], 'idx_pd_dog_videos_uploaded_by');
            $table->index(['owner_id'], 'idx_pd_dog_videos_owner');
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->dropColumn(['uploaded_by_user_id', 'owner_id']);
        });
    }
};