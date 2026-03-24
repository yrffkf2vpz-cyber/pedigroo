<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_championships', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('title_definition_id');

            $table->unsignedBigInteger('country_id')->nullable();

            $table->date('date')->nullable();

            $table->string('source', 100)->nullable();
            $table->string('external_id', 191)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('dog_id');
            $table->index('event_id');
            $table->index('title_definition_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_championships');
    }
};
