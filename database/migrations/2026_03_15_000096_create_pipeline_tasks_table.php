<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('type', 255);

            $table->longText('payload')->nullable();

            $table->enum('status', [
                'pending',
                'running',
                'done',
                'error'
            ])->default('pending');

            $table->text('log')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_tasks');
    }
};
