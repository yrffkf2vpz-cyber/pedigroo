<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');

            $table->unsignedBigInteger('document_type_id');

            $table->string('title', 255)->nullable();

            $table->string('file_reference', 500)->nullable();

            $table->date('issued_at')->nullable();

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('document_type_id');

         });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_documents');
    }
};
