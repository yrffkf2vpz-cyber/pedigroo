<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_standard_measurements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_standard_id');

            $table->string('metric', 100); // pl. "height_male", "weight_female"
            $table->decimal('min_value', 6, 2)->nullable();
            $table->decimal('max_value', 6, 2)->nullable();
            $table->string('unit', 20)->default('cm'); // cm, kg, degree, ratio

            $table->timestamps();

            $table->unique(['breed_standard_id', 'metric'], 'standard_metric_unique');

            $table->foreign('breed_standard_id')
                ->references('id')
                ->on('pd_breed_standards')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_standard_measurements');
    }
};