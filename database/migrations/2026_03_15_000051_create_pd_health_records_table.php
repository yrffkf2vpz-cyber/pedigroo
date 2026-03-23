<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('record_type_id')->nullable();
            $table->unsignedBigInteger('result_code_id')->nullable();

            $table->timestamps();

            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');

            // record_type_id és result_code_id
            // jelenleg csak hivatkozások, FK késobb
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_records');
    }
};
