<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registry_prefixes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('country_code', 10);
            $table->string('prefix', 50);

            $table->enum('classification', [
                'modern',
                'historical',
                'legacy'
            ]);

            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->text('notes')->nullable();

            // HELYES datetime mezok
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexek
            $table->index(['country_code']);
            $table->index(['prefix']);
            $table->index(['classification']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registry_prefixes');
    }
};
