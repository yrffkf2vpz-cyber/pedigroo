<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breeding_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('male_dog_id');
            $table->unsignedBigInteger('female_dog_id');

            $table->date('mating_date');
            $table->date('litter_birth_date')->nullable();

            $table->string('litter_code', 50)->nullable();
            $table->string('status', 50)->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('male_dog_id');
            $table->index('female_dog_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breeding_records');
    }
};
