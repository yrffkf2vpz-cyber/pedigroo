<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_files', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('module', 255);
            $table->string('task', 255);
            $table->string('file_path', 255);
            $table->string('hash', 255)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_files');
    }
};
