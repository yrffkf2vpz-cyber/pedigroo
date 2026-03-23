<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->foreign('uploaded_by_user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('owner_id')
                ->references('id')->on('pd_owners')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_videos', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by_user_id']);
            $table->dropForeign(['owner_id']);
        });
    }
};