<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_standard_colors', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_standard_id');

            $table->string('color', 100); // pl. "Black and Tan"
            $table->string('pattern', 100)->nullable(); // pl. "Brindle", "Piebald"

            $table->timestamps();

            $table->unique(['breed_standard_id', 'color', 'pattern'], 'standard_color_unique');

            $table->foreign('breed_standard_id')
                ->references('id')
                ->on('pd_breed_standards')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_standard_colors');
    }
};