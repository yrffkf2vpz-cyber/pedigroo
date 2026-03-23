<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_vaccinations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->date('date');

            $table->string('vaccine_name', 255)->nullable();
            $table->string('batch_number', 255)->nullable();
            $table->date('valid_until')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['date']);
            $table->index(['vaccine_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_vaccinations');
    }
};
