<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_health_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->string('type', 100)->nullable();
            $table->date('issued_at')->nullable();
            $table->string('issued_by', 255)->nullable();

            $table->string('file_path', 500)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('dog_id')
                  ->references('id')
                  ->on('pd_dogs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_health_certificates');
    }
};
