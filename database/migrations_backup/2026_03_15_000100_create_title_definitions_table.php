<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('title_definitions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('global_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            $table->string('title_code', 255)->nullable();
            $table->string('title_name', 255);

            $table->text('requirement')->nullable();

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['global_id']);
            $table->index(['country_id']);
            $table->index(['title_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('title_definitions');
    }
};
