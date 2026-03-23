<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_rings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('source', 100);

            $table->string('external_id', 191)->nullable();
            $table->string('name', 255)->nullable();

            $table->longText('raw')->nullable();
            $table->string('hash', 64)->nullable();

            $table->tinyInteger('confidence')->nullable();
            $table->dateTime('checked_at')->nullable();

            $table->text('notes')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['external_id']);
            $table->index(['hash']);
            $table->index(['source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_rings');
    }
};
