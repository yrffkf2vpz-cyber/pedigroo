<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_diagnosis_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('code', 50)->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_diagnosis_categories');
    }
};