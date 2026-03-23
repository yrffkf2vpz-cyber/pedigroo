<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            // kinek szól az értesítés
            $table->unsignedBigInteger('user_id');

            // polymorphic target (pl. kutya, kennel, alom, show, stb.)
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('notifiable_type', 100)->nullable();

            // értesítés típusa (pl. new_litter, new_favorite, show_status, timeline_event)
            $table->string('type', 100);

            // opcionális adat (JSON)
            $table->json('data')->nullable();

            // olvasottság
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // mikor jött létre
            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_notif_user');
            $table->index(['notifiable_id', 'notifiable_type'], 'idx_pd_notif_target');
            $table->index(['type'], 'idx_pd_notif_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_notifications');
    }
};