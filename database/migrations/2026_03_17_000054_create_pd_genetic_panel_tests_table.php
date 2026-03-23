<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_genetic_panel_tests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('genetic_test_id');

            $table->timestamps();

            $table->unique(['panel_id', 'genetic_test_id'], 'panel_test_unique');

            $table->foreign('panel_id')
                ->references('id')
                ->on('pd_genetic_panels')
                ->onDelete('cascade');

            $table->foreign('genetic_test_id')
                ->references('id')
                ->on('pd_genetic_tests')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_genetic_panel_tests');
    }
};