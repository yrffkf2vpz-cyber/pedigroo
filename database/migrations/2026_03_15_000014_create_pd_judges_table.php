<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_judges', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('full_name', 255);

            $table->string('last_name', 255)->nullable();
            $table->string('first_name', 255)->nullable();

            $table->string('country', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_judges');
    }
};
