<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('authority_id'); // FCI, AKC, KC, UKC, CKC, ANKC, stb.
            $table->string('code', 50);                 // pl. "Group 1", "Hound Group"
            $table->string('name', 255);                // pl. "Herding Dogs"

            $table->timestamps();

            $table->unique(['authority_id', 'code'], 'breed_group_unique');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_groups');
    }
};