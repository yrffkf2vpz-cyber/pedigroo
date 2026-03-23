<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_country_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('alias', 255)->unique();

            $table->unsignedBigInteger('country_id');

            $table->timestamps();

            $table->foreign('country_id')
                  ->references('id')
                  ->on('pd_countries')
                  ->onDelete('cascade');

            $table->index('country_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_country_aliases');
    }
};
