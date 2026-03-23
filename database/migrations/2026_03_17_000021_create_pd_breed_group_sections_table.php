<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_group_sections', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_group_id');
            $table->string('code', 50);      // pl. "Section 1"
            $table->string('name', 255);     // pl. "Sheepdogs"

            $table->timestamps();

            $table->unique(['breed_group_id', 'code'], 'group_section_unique');

            $table->foreign('breed_group_id')
                ->references('id')
                ->on('pd_breed_groups')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_group_sections');
    }
};