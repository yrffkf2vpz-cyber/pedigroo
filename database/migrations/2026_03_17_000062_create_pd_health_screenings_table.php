<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_screenings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('condition_id');

            $table->string('method', 255); // pl. "X-ray", "OFA", "ECVO", "Cardiac Auscultation"
            $table->string('recommended_age', 50)->nullable(); // pl. "12 months", "2 years"
            $table->text('details')->nullable();

            $table->timestamps();

            $table->foreign('condition_id')
                ->references('id')
                ->on('pd_health_conditions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_screenings');
    }
};