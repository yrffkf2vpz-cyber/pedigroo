<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_standard_sections', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_standard_id');

            $table->string('title', 255); // pl. "Head", "Body", "Movement"
            $table->text('content')->nullable();

            $table->integer('order')->default(0);

            $table->timestamps();

            $table->foreign('breed_standard_id')
                ->references('id')
                ->on('pd_breed_standards')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_standard_sections');
    }
};