<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_kennels', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('prefix', 255)->nullable();
            $table->string('suffix', 255)->nullable();

            $table->string('registration_number', 100)->nullable();
            $table->string('registry_authority', 100)->nullable();

            $table->string('country_code', 2)->nullable();
            $table->string('city', 255)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_code', 'city'], 'idx_pd_kennels_country_city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_kennels');
    }
};