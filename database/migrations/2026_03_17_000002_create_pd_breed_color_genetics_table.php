<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_color_genetics', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id')->nullable();
            $table->string('gene', 16);
            $table->string('genotype', 64);
            $table->text('description')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->date('updated_at')->nullable(); // eredeti SQL szerint

            // FK opcionális
            // $table->foreign('breed_id')->references('id')->on('pd_breeds')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::create('pd_breed_color_genetics', function (Blueprint $table) {
    $table->bigIncrements('id');

    $table->unsignedBigInteger('breed_id')->nullable();
    $table->string('gene', 16);
    $table->string('genotype', 64);
    $table->text('description')->nullable();

    $table->timestamp('created_at')->nullable();
    $table->date('updated_at')->nullable();

    $table->foreign('breed_id')
        ->references('id')
        ->on('pd_breeds')
        ->onDelete('set null');
});
    }
};