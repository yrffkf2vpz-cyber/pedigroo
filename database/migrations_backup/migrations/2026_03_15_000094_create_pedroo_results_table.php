<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('external_id', 191)->nullable();
            $table->string('source', 100);

            $table->unsignedBigInteger('event_id')->nullable();

            $table->string('dog_name', 255)->nullable();
            $table->string('class_type', 255)->nullable();
            $table->string('placement', 100)->nullable();

            $table->longText('raw')->nullable();
            $table->string('hash', 64)->nullable();

            $table->enum('status', [
                'pending',
                'promoted',
                'approved',
                'rejected',
                'error'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('submitted_by')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['external_id']);
            $table->index(['hash']);
            $table->index(['event_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_results');
    }
};
