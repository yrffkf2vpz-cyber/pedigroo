<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_timeline_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->date('date')->nullable();
            $table->string('type', 50);
            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index('breed_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_timeline_events');
    }
};