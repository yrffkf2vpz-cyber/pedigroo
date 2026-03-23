<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_condition_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('condition_id');
            $table->string('alias', 255);

            $table->timestamps();

            $table->unique(['condition_id', 'alias'], 'condition_alias_unique');

            $table->foreign('condition_id')
                ->references('id')
                ->on('pd_health_conditions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_condition_aliases');
    }
};