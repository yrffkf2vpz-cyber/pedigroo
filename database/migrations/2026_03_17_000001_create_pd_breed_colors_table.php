<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_colors', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->string('color_name', 100);
            $table->boolean('fci_accepted')->default(true);
            $table->string('notes', 255)->nullable();

            $table->timestamps();

            // FK opcionális – csak akkor, ha létezik pd_breeds
            // $table->foreign('breed_id')->references('id')->on('pd_breeds')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::create('pd_breed_colors', function (Blueprint $table) {
    $table->bigIncrements('id');

    $table->unsignedBigInteger('breed_id');
    $table->string('color_name', 100);
    $table->boolean('fci_accepted')->default(true);
    $table->string('notes', 255)->nullable();

    $table->timestamps();

    $table->foreign('breed_id')
        ->references('id')
        ->on('pd_breeds')
        ->onDelete('cascade');
});
    }
};