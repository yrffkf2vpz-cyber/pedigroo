<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_devices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->string('device_name')->nullable(); // "iPhone 14", "Chrome on Windows"
            $table->string('fingerprint')->unique();   // device fingerprint hash

            $table->boolean('is_default')->default(false);

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            // Indexek
            $table->index('user_id');
            $table->index('fingerprint');

            // FK
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_devices');
    }
};
