<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedroo_pdf_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type'); // health, event, pedigree, stb.
            $table->string('source')->nullable(); // pl. 'MEOESZ', 'SVK', 'SKK'
            $table->string('file_path');
            $table->string('status')->default('pending'); // pending, running, done, failed
            $table->json('stats')->nullable(); // összesítés: total, created, updated, errors
            $table->json('log')->nullable();   // részletes log
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_pdf_imports');
    }
};
