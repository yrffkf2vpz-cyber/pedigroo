<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_access_audit_log', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kennel_id');
            $table->unsignedBigInteger('dog_id')->nullable();

            $table->enum('action', ['attempt', 'allowed', 'denied', 'expired']);
            $table->string('reason', 255)->nullable();

            $table->timestamp('created_at')->nullable();

            // Indexek
            $table->index('user_id');
            $table->index('kennel_id');
            $table->index('dog_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_access_audit_log');
    }
};