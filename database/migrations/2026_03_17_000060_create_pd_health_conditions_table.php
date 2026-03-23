<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code', 100); // pl. "HD", "ED", "PRA", "CEA"
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable(); // pl. "orthopedic", "ocular", "cardiac"

            $table->timestamps();

            $table->unique(['code'], 'health_condition_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_conditions');
    }
};