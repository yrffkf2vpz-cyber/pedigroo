<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_kennel_members', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('kennel_id');
            $table->unsignedBigInteger('owner_id');

            $table->enum('role', ['owner', 'co_owner', 'manager'])->default('owner');

            $table->timestamps();

            $table->unique(['kennel_id', 'owner_id'], 'uq_pd_kennel_members_pair');

            $table->index(['kennel_id'], 'idx_pd_kennel_members_kennel');
            $table->index(['owner_id'], 'idx_pd_kennel_members_owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_kennel_members');
    }
};