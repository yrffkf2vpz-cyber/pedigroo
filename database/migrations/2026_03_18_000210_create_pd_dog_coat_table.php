<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_coat', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('coat_type', 100)->nullable();
            $table->string('coat_color', 255)->nullable();
            $table->string('pattern', 255)->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_coat_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_coat');
    }
};