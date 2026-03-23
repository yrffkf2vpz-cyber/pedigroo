<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_compliance_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');

            $table->string('type', 100)->nullable();
            $table->string('status', 50)->nullable();

            $table->date('checked_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('entity_type');
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_compliance_items');
    }
};
