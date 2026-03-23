<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_health_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FIGYELEM: dog_id jelenleg varchar(255) — így hagyjuk, mert a pedroo rétegben
            // lehet név, külso ID, vagy bármi nyers adat.
            $table->string('dog_id', 255);

            $table->string('type', 255);
            $table->string('value', 255);

            $table->date('date')->nullable();
            $table->string('lab', 255)->nullable();

            $table->string('source', 255);

            // HELYES timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexek
            $table->index(['dog_id']);
            $table->index(['type']);
            $table->index(['source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_health_records');
    }
};
