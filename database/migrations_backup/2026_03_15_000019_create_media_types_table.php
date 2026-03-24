<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_types', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();

            $table->string('slug', 50)->unique();
            $table->string('name', 100);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_types');
    }
};
