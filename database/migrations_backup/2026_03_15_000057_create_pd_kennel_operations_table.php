<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_kennel_operations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('kennel_id');

            $table->date('date');

            $table->string('type', 100)->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('kennel_id')
                  ->references('id')
                  ->on('pd_kennels')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_kennel_operations');
    }
};
