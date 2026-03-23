<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_diagnoses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('code', 50)->unique()->nullable();

            $table->unsignedBigInteger('category_id')->nullable();

            $table->timestamps();

            $table->index('category_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('pd_diagnosis_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_diagnoses');
    }
};