<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_placements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code', 50);
            $table->string('label', 255);

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_placements');
    }
};
