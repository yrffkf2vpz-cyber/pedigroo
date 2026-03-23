<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_favorites', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki kedvencelte
            $table->unsignedBigInteger('user_id');

            // polymorphic target
            $table->unsignedBigInteger('favoritable_id');
            $table->string('favoritable_type', 100);

            // gyors ajánlórendszerhez
            $table->timestamp('favorited_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_fav_user');
            $table->index(['favoritable_id', 'favoritable_type'], 'idx_pd_fav_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_favorites');
    }
};