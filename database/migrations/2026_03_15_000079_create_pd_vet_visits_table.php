<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_vet_visits', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');

            $table->date('visit_date');

            $table->string('vet_name', 255)->nullable();
            $table->string('reason', 255)->nullable();

            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['visit_date']);
            $table->index(['vet_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_vet_visits');
    }
};
