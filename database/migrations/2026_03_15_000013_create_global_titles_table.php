<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_titles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code', 50);
            $table->string('name', 255);
            $table->string('category', 100);
            $table->string('subcategory', 100)->nullable();
            $table->string('organization', 50)->nullable();
            $table->text('description')->nullable();

            $table->tinyInteger('is_active')->default(1);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_titles');
    }
};
