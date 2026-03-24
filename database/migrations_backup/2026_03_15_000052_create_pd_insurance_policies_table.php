<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_insurance_policies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');

            $table->string('provider', 255)->nullable();
            $table->string('policy_number', 255)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_insurance_policies');
    }
};
