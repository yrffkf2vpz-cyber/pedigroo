<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_behavior_tests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dog_id');
            $table->unsignedBigInteger('test_type_id')->nullable(); // behavior_test_types
            $table->unsignedBigInteger('event_id')->nullable();

            $table->date('tested_at')->nullable();
            $table->string('location', 255)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['dog_id'], 'idx_pd_behavior_tests_dog');
            $table->index(['test_type_id'], 'idx_pd_behavior_tests_type');
            $table->index(['event_id'], 'idx_pd_behavior_tests_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_behavior_tests');
    }
};