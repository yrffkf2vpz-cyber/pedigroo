<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_coi', function (Blueprint $table) {
            $table->unsignedBigInteger('dog_id');

            $table->decimal('coi', 6, 4);

            $table->timestamp('calculated_at')->nullable();

            $table->primary('dog_id');

          });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_coi');
    }
};
