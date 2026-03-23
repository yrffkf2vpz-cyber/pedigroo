<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_medications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->date('date');

            $table->string('name', 255)->nullable();
            $table->string('dose', 255)->nullable();
            $table->string('route', 100)->nullable(); // pl. oral, injection, topical

            $table->integer('duration_days')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_medications');
    }
};
