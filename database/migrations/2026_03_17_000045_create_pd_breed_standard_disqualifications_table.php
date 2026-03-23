<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_standard_disqualifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_standard_id');

            $table->string('category', 100)->nullable(); // pl. "Color", "Temperament"
            $table->text('description');

            $table->timestamps();

            $table->foreign('breed_standard_id')
                ->references('id')
                ->on('pd_breed_standards')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_standard_disqualifications');
    }
};