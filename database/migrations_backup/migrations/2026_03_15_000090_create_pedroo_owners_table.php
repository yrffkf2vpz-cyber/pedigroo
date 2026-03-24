<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_owners', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255)->nullable();

            $table->unsignedBigInteger('name_order_id')->nullable();

            $table->string('normalized_name', 255)->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('kennel_id')->nullable();

            $table->text('raw_owner_string')->nullable();

            // országkód (nyers)
            $table->string('country', 10)->nullable();

            // HELYES datetime mezok
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['normalized_name']);
            $table->index(['country_id']);
            $table->index(['kennel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_owners');
    }
};
