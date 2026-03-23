<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('detected_type', 255);
            $table->string('raw_value', 255);

            $table->text('suggested_rule')->nullable();

            $table->integer('occurrences')->default(0);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'applied'
            ])->default('pending');

            $table->unsignedBigInteger('breed_id')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['detected_type']);
            $table->index(['raw_value']);
            $table->index(['status']);
            $table->index(['breed_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_suggestions');
    }
};
