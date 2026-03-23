<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_training_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->date('date');

            $table->string('type', 100)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('result', 100)->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['date']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_training_records');
    }
};
