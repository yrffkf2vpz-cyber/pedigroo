<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('type', 100); // pedigree, export_pedigree, health_certificate, etc.
            $table->string('file_path', 500);

            $table->date('issued_at')->nullable();
            $table->string('issuer', 255)->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_dog_documents_dog');
            $table->index(['type'], 'idx_pd_dog_documents_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_documents');
    }
};