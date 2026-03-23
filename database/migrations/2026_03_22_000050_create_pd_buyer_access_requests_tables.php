<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_buyer_access_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('kennel_id');

            // pet_home / breeding / show / other
            $table->string('purpose', 50)->nullable();

            $table->text('message')->nullable();

            // pending / approved / rejected
            $table->string('status', 50)->default('pending');

            // meta
            $table->string('ip_address', 50)->nullable();
            $table->string('device_fingerprint', 255)->nullable();

            $table->timestamps();

            $table->index(['buyer_id'], 'idx_buyer_access_requests_buyer');
            $table->index(['dog_id'], 'idx_buyer_access_requests_dog');
            $table->index(['kennel_id'], 'idx_buyer_access_requests_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_buyer_access_requests');
    }
};