<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_learning_queue', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('domain', 50);

            $table->string('raw_input', 255);
            $table->string('normalized_input', 255)->nullable();
            $table->string('ai_suggestion', 255)->nullable();

            $table->longText('context')->nullable();

            $table->enum('status', ['NEW', 'CONFIRMED', 'REJECTED'])
                  ->default('NEW');

            $table->unsignedInteger('count')->default(1);

            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');

            $table->timestamps();

            $table->index(['domain', 'status']);
            $table->index(['domain', 'raw_input']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_learning_queue');
    }
};
