<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_titles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('title_code', 50);
            $table->string('title_name', 255);

            $table->date('awarded_at')->nullable();
            $table->string('awarded_by', 255)->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_titles_dog');
            $table->index(['title_code'], 'idx_pd_dog_titles_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_titles');
    }
};