<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pending_parents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pending_dog_id');

            $table->unsignedBigInteger('sire_id')->nullable();
            $table->unsignedBigInteger('dam_id')->nullable();

            $table->enum('match_status', ['matched', 'fuzzy', 'unknown'])->default('unknown');
            $table->float('confidence')->default(0);

            $table->unsignedBigInteger('created_from_pedroo_id')->nullable();

            $table->enum('activation_status', ['pending', 'activated', 'expired'])->default('pending');
            $table->date('protected_until');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pending_parents');
    }
};