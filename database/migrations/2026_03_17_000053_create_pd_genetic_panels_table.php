<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_genetic_panels', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('lab_id')->nullable();

            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('lab_id')
                ->references('id')
                ->on('pd_genetic_labs')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_genetic_panels');
    }
};