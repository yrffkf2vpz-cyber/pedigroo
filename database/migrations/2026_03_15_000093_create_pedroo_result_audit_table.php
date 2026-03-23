<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_result_audit', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('pedroo_result_id');
            $table->string('action', 100);

            $table->unsignedBigInteger('actor_id')->nullable();

            $table->longText('payload')->nullable();

            // HELYES timestamp
            $table->timestamp('created_at')->nullable();

            // Indexek
            $table->index(['pedroo_result_id']);
            $table->index(['actor_id']);
            $table->index(['action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_result_audit');
    }
};
