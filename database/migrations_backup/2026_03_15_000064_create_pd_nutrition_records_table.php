<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_nutrition_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->date('date');

            $table->string('food_name', 255)->nullable();
            $table->string('brand', 255)->nullable();

            $table->integer('amount_grams')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_nutrition_records');
    }
};
