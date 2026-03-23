<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_measurements', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Kapcsolat a fajtához
            $table->unsignedBigInteger('breed_id');

            // Marmagasság (cm)
            $table->unsignedInteger('height_min')->nullable();
            $table->unsignedInteger('height_max')->nullable();

            // Testsúly (kg)
            $table->unsignedInteger('weight_min')->nullable();
            $table->unsignedInteger('weight_max')->nullable();

            // Testhossz (cm)
            $table->unsignedInteger('length_min')->nullable();
            $table->unsignedInteger('length_max')->nullable();

            // Mellkas mélység (cm)
            $table->unsignedInteger('chest_min')->nullable();
            $table->unsignedInteger('chest_max')->nullable();

            // Megjegyzések
            $table->text('notes')->nullable();

            $table->timestamps();

            // Index
            $table->index('breed_id');

            // Foreign key
            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_measurements');
    }
};