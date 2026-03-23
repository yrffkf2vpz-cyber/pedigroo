<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code', 50)->nullable();
            $table->string('name', 100);
            $table->string('category', 50)->nullable();
            $table->string('subcategory', 50)->nullable();
            $table->string('organization', 50)->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Egyedi indexek
            $table->unique('code');
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
