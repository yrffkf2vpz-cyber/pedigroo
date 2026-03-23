<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki végezte az aktivitást
            $table->unsignedBigInteger('user_id');

            // milyen típusú aktivitás (pl. view_dog, view_kennel, search, favorite, open_module)
            $table->string('action', 100);

            // polymorphic target (pl. kutya, kennel, alom, fajta, show, stb.)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 100)->nullable();

            // opcionális extra adat (pl. keresési kifejezés, modul neve)
            $table->json('metadata')->nullable();

            // mikor történt
            $table->timestamp('occurred_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_activity_user');
            $table->index(['action'], 'idx_pd_activity_action');
            $table->index(['subject_id', 'subject_type'], 'idx_pd_activity_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_activity_log');
    }
};