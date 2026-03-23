<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pending_kennels', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('country')->nullable();
            $table->string('owner_name_raw')->nullable();

            $table->unsignedBigInteger('created_from_pedroo_id')->nullable();

            $table->enum('activation_status', ['pending', 'activated', 'expired'])->default('pending');
            $table->string('activation_token', 64)->nullable();
            $table->date('protected_until');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pending_kennels');
    }
};