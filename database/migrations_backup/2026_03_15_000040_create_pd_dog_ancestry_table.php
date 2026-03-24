<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_ancestry', function (Blueprint $table) {
            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('ancestor_id');

            $table->unsignedInteger('generations');

            $table->primary(['dog_id', 'ancestor_id']);

            $table->index('ancestor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_ancestry');
    }
};
