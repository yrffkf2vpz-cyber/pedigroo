<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_pdf_imports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('type', 255);
            $table->string('source', 255)->nullable();

            $table->string('file_path', 255);

            // pending / processing / done / failed
            $table->string('status', 255)->default('pending');

            $table->json('stats')->nullable();
            $table->json('log')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['type']);
            $table->index(['user_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_pdf_imports');
    }
};
