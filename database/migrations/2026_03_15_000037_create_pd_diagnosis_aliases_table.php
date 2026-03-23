<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_diagnosis_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('alias', 255)->unique();

            $table->unsignedBigInteger('diagnosis_id');

            $table->timestamps();

            $table->index('diagnosis_id');

            $table->foreign('diagnosis_id')
                ->references('id')
                ->on('pd_diagnoses')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_diagnosis_aliases');
    }
};
