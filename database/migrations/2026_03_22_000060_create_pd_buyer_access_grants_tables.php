<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_buyer_access_grants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('kennel_id');

            // mikor jár le a hozzáférés (opcionális)
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(['buyer_id'], 'idx_buyer_access_grants_buyer');
            $table->index(['dog_id'], 'idx_buyer_access_grants_dog');
            $table->index(['kennel_id'], 'idx_buyer_access_grants_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_buyer_access_grants');
    }
};