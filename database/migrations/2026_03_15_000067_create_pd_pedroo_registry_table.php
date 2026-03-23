<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pedroo_registry', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('entity_type', 50);
            $table->string('entity_name', 255);

            $table->string('module', 100)->nullable();

            $table->string('status', 50)->default('ok');

            $table->json('details')->nullable();

            $table->timestamps();

            $table->index(['entity_type']);
            $table->index(['module']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pedroo_registry');
    }
};
