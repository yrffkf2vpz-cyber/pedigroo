<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_group_links', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('breed_group_id');
            $table->unsignedBigInteger('breed_group_section_id')->nullable();

            $table->timestamps();

            $table->unique(
                ['breed_id', 'breed_group_id', 'breed_group_section_id'],
                'breed_group_link_unique'
            );

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('breed_group_id')
                ->references('id')
                ->on('pd_breed_groups')
                ->onDelete('cascade');

            $table->foreign('breed_group_section_id')
                ->references('id')
                ->on('pd_breed_group_sections')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_group_links');
    }
};