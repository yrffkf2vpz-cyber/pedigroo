<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_rule_suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('detected_type', 255);
            $table->string('raw_value', 255);
            $table->string('suggested_rule', 255);

            $table->unsignedBigInteger('breed_id')->nullable();

            $table->integer('occurrences')->default(0);

            $table->string('status', 50)->default('pending');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['detected_type']);
            $table->index(['breed_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_rule_suggestions');
    }
};
