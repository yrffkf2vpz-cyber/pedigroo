<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_boarding', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('kennel_id')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->string('type', 50)->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('dog_id');
            $table->index('kennel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_boarding');
    }
};
