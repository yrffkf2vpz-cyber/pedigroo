<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_search_index', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            // denormalizált mezok
            $table->string('name', 255)->nullable();
            $table->string('breed_name', 255)->nullable();
            $table->string('breed_group', 255)->nullable();
            $table->string('kennel_name', 255)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('sex', 10)->nullable();
            $table->year('birth_year')->nullable();

            // státuszok
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);

            // gyors kereséshez
            $table->text('keywords')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_search_dog');
            $table->index(['breed_name'], 'idx_pd_search_breed');
            $table->index(['kennel_name'], 'idx_pd_search_kennel');
            $table->index(['country_code'], 'idx_pd_search_country');
            $table->index(['birth_year'], 'idx_pd_search_birthyear');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_search_index');
    }
};