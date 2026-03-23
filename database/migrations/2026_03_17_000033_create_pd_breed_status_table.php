<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_status', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_id');
            $table->unsignedBigInteger('authority_id')->nullable();

            $table->string('status', 50); 
            // pl. "extinct", "developing", "provisional", "recognized"

            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->timestamps();

            $table->unique(['breed_id', 'authority_id'], 'breed_status_unique');

            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_status');
    }
};