<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_origins', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('primary_country_id')->nullable();

            $table->text('history')->nullable();
            $table->text('original_function')->nullable();
            $table->text('cultural_notes')->nullable();

            $table->timestamps();

            $table->unique(['breed_id'], 'breed_origin_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('primary_country_id')
                ->references('id')
                ->on('pd_countries')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_origins');
    }
};