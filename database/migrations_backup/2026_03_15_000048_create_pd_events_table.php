<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);

            $table->string('country', 10)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('venue', 255)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('event_type', 100)->nullable();
            $table->string('organizer', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_events');
    }
};
