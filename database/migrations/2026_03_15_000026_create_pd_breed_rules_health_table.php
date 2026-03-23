<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_rules_health', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('breed_id');

            $table->string('test_code', 50);
            $table->string('test_type', 50)->nullable(); // orthopedic, genetic, eye, etc.

            $table->string('min_result', 50)->nullable();
            $table->string('max_result', 50)->nullable();

            $table->boolean('mandatory')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_rules_health');
    }
};
