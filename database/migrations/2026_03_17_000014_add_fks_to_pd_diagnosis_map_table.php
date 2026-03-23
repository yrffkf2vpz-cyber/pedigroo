<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_diagnosis_map', function (Blueprint $table) {
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
        Schema::table('pd_diagnosis_map', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['diagnosis_id']);
            $table->dropForeign(['category_id']);
        });
    }
};