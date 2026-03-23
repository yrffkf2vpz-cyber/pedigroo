<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_locations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('country', 10);
            $table->string('city', 255);
            $table->string('venue', 255)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            $table->index(['country', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_locations');
    }
};
