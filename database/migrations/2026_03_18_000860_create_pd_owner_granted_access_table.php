<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_owner_granted_access', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki kapta az engedélyt
            $table->unsignedBigInteger('user_id');

            // melyik kennel adta
            $table->unsignedBigInteger('kennel_id');

            // mire kapott engedélyt
            $table->unsignedBigInteger('dog_id')->nullable();
            $table->unsignedBigInteger('litter_id')->nullable();

            // típus: coi / pedigree / parents / health_tests / full_family
            $table->string('access_type', 50);

            // meddig érvényes (pl. 24 óra)
            $table->timestamp('expires_at')->nullable();

            // aktív-e
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_granted_user');
            $table->index(['kennel_id'], 'idx_pd_granted_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_owner_granted_access');
    }
};