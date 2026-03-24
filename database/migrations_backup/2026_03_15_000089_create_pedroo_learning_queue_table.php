<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_learning_queue', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('source_table', 50);
            $table->string('source_column', 50);

            $table->text('raw_value');

            $table->string('detected_type', 50);

            $table->unsignedTinyInteger('confidence')->default(0);

            // pl. pending / processed / failed / ignored
            $table->string('status', 20)->default('pending');

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['source_table']);
            $table->index(['source_column']);
            $table->index(['detected_type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_learning_queue');
    }
};
