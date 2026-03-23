<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('type', 100);
            $table->string('scope', 100)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('breed', 100)->nullable();
            $table->string('registry', 100)->nullable();
            $table->integer('year')->nullable();
            $table->date('date')->nullable();

            $table->string('title_key', 255);
            $table->string('description_key', 255)->nullable();

            $table->longText('params')->nullable();
            $table->string('value_before', 255)->nullable();
            $table->string('value_after', 255)->nullable();
            $table->longText('meta')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek a DESCRIBE alapján
            $table->index('type');
            $table->index('scope');
            $table->index('code');
            $table->index('breed');
            $table->index('registry');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_events');
    }
};
