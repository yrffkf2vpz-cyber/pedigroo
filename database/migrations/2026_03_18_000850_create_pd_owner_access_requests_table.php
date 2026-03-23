<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_owner_access_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki kér hozzáférést
            $table->unsignedBigInteger('user_id');

            // melyik kennelhez tartozó kutya/almok
            $table->unsignedBigInteger('kennel_id');

            // mire kér hozzáférést
            $table->unsignedBigInteger('dog_id')->nullable();
            $table->unsignedBigInteger('litter_id')->nullable();

            // típus: coi / pedigree / parents / health_tests / full_family
            $table->string('access_type', 50);

            // státusz: pending / approved / denied
            $table->string('status', 20)->default('pending');

            // kennel tulajdonos megjegyzése
            $table->text('owner_note')->nullable();

            // buyer megjegyzése
            $table->text('buyer_note')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_access_req_user');
            $table->index(['kennel_id'], 'idx_pd_access_req_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_owner_access_requests');
    }
};
