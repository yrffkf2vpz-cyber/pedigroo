<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_kennel_facilities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('kennel_id');

            $table->integer('area_m2')->nullable();
            $table->integer('indoor_runs')->nullable();
            $table->integer('outdoor_runs')->nullable();

            $table->boolean('has_quarantine')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('kennel_id')
                  ->references('id')
                  ->on('pd_kennels')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_kennel_facilities');
    }
};
