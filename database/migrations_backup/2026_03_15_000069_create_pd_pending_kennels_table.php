<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pending_kennels', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('country', 255)->nullable();

            // Nyers owner név, amíg nincs normalizálva
            $table->string('owner_name_raw', 255)->nullable();

            // Ha egy meglévo Pedroo kennel alapján jött létre
            $table->unsignedBigInteger('created_from_pedroo_id')->nullable();

            // Aktiválási workflow
            $table->enum('activation_status', ['pending', 'activated', 'expired'])
                  ->default('pending');

            $table->string('activation_token', 64)->nullable();

            // Jogi védelem (pl. 15 év szabály)
            $table->date('protected_until');

            $table->timestamps();

            // Indexek
            $table->index(['activation_status']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pending_kennels');
    }
};
