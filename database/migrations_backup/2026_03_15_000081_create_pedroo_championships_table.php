<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_championships', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('dog_name', 255)->nullable();
            $table->unsignedBigInteger('dog_id')->nullable();

            $table->string('event_name', 255)->nullable();
            $table->unsignedBigInteger('event_id')->nullable();

            $table->string('title_code', 50)->nullable();
            $table->string('title_name', 255)->nullable();
            $table->unsignedBigInteger('title_definition_id')->nullable();

            $table->string('country', 10)->nullable();
            $table->date('date')->nullable();

            $table->string('source', 100)->nullable();
            $table->string('external_id', 191)->nullable();

            $table->longText('raw')->nullable();
            $table->string('hash', 64)->nullable();

            $table->tinyInteger('confidence')->nullable();

            $table->enum('status', ['pending', 'promoted', 'error'])
                  ->default('pending');

            $table->text('notes')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['event_id']);
            $table->index(['title_definition_id']);
            $table->index(['hash']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_championships');
    }
};
