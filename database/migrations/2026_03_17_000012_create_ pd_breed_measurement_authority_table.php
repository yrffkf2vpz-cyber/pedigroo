<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_measurement_authority', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_measurement_id');
            $table->unsignedBigInteger('authority_id');

            $table->timestamps();

            // Rövidített indexnév
            $table->index(
                ['breed_measurement_id', 'authority_id'],
                'bm_auth_idx'
            );

            // FK-k
            $table->foreign('breed_measurement_id')
                ->references('id')
                ->on('pd_breed_measurements')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_measurement_authority');
    }
};