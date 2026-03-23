<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_diagnosis_map', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('diagnosis_id');

            $table->string('raw_value', 255);

            $table->unsignedBigInteger('category_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['breed_id', 'diagnosis_id']);
            $table->index('category_id');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('diagnosis_id')
                ->references('id')
                ->on('pd_diagnoses')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('pd_diagnosis_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_diagnosis_map');
    }
};
