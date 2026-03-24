<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_families', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('related_dog_id');

            $table->string('relation_type', 50);

            $table->timestamps();

            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');

            $table->foreign('related_dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');

            $table->unique(['dog_id', 'related_dog_id', 'relation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_families');
    }
};
