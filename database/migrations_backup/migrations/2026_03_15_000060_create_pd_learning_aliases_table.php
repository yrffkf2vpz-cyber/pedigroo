<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_learning_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('domain', 50);
            $table->string('alias', 255);
            $table->string('canonical', 255);

            $table->timestamps();

            $table->unique(['domain', 'alias']);
            $table->index(['domain', 'canonical']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_learning_aliases');
    }
};
