<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_standards', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('authority_id'); // FCI, AKC, KC, stb.

            $table->string('version', 50)->nullable(); // pl. "2024 revision"
            $table->date('published_at')->nullable();

            $table->text('general_appearance')->nullable();
            $table->text('temperament')->nullable();

            $table->timestamps();

            $table->unique(['breed_id', 'authority_id'], 'breed_standard_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_standards');
    }
};