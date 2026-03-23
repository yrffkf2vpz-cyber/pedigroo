<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_owner_access_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki nézte meg
            $table->unsignedBigInteger('user_id');

            // melyik kennelhez tartozó adatot
            $table->unsignedBigInteger('kennel_id');

            // mit nézett meg
            $table->unsignedBigInteger('dog_id')->nullable();
            $table->unsignedBigInteger('litter_id')->nullable();

            // típus: coi / pedigree / parents / health_tests / full_family
            $table->string('access_type', 50);

            // mikor
            $table->timestamp('accessed_at')->nullable();

            // metaadatok: IP, device, session
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_access_log_user');
            $table->index(['kennel_id'], 'idx_pd_access_log_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_owner_access_log');
    }
};
