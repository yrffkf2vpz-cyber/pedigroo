<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_connections', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki követ
            $table->unsignedBigInteger('user_id');

            // mit követ (polymorphic)
            $table->unsignedBigInteger('connectable_id');
            $table->string('connectable_type', 100);

            // mikor kezdte követni
            $table->timestamp('followed_at')->nullable();

            // opcionális metaadat (pl. "source": "recommendation")
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_conn_user');
            $table->index(['connectable_id', 'connectable_type'], 'idx_pd_conn_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_connections');
    }
};