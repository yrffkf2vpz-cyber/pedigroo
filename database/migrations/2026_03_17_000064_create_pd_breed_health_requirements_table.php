<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_health_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('screening_id');
            $table->unsignedBigInteger('authority_id')->nullable();

            $table->string('requirement_level', 50)->nullable(); // pl. "mandatory", "recommended"
            $table->string('frequency', 50)->nullable(); // pl. "every 2 years"

            $table->timestamps();

            $table->unique(['breed_id', 'screening_id', 'authority_id'], 'breed_screening_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('screening_id')
                ->references('id')
                ->on('pd_health_screenings')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_health_requirements');
    }
};