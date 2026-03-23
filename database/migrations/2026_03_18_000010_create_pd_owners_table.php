<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_owners', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('full_name', 255);
            $table->string('short_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();

            $table->string('country_code', 2)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('street_address', 255)->nullable();

            $table->boolean('is_kennel_owner')->default(false);
            $table->boolean('is_handler')->default(false);
            $table->boolean('is_breeder')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_code', 'city'], 'idx_pd_owners_country_city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_owners');
    }
};