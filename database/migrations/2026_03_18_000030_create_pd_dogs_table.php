<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dogs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('registration_number', 100)->nullable();
            $table->string('microchip_number', 100)->nullable();

            $table->unsignedBigInteger('breed_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('kennel_id')->nullable();

            $table->enum('sex', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('color', 255)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['breed_id', 'sex'], 'idx_pd_dogs_breed_sex');
            $table->index(['owner_id'], 'idx_pd_dogs_owner');
            $table->index(['kennel_id'], 'idx_pd_dogs_kennel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dogs');
    }
};