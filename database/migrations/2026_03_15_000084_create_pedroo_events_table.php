<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('source', 100);

            $table->string('external_id', 191)->nullable();
            $table->string('name', 255)->nullable();

            $table->string('country', 10)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('venue', 255)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('event_type', 100)->nullable();

            $table->longText('raw')->nullable();
            $table->string('hash', 64)->nullable();

            $table->tinyInteger('confidence')->nullable();
            $table->dateTime('checked_at')->nullable();

            $table->text('notes')->nullable();

            // HELYES timestamps
            $table->timestamps();

            // Indexek
            $table->index(['external_id']);
            $table->index(['hash']);
            $table->index(['source']);
            $table->index(['start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_events');
    }
};
