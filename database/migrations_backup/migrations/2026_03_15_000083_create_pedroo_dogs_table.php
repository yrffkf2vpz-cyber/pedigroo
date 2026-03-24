<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedroo_dogs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Szülok
            $table->unsignedBigInteger('father_id')->nullable();
            $table->unsignedBigInteger('mother_id')->nullable();

            // Forrásadatok
            $table->string('source_dog_id', 255)->nullable();
            $table->string('source_name', 255)->nullable();
            $table->string('source_reg_no', 50)->nullable();
            $table->string('source_fci_no', 50)->nullable();

            // Történeti besorolás
            $table->enum('history_classification', ['modern', 'historical', 'legacy'])->nullable();

            // Regisztrációs ország
            $table->string('reg_country', 5)->nullable();

            // Valódi név és komponensei
            $table->string('real_name', 255)->nullable();
            $table->string('kennel_name', 255)->nullable();
            $table->string('real_prefix', 100)->nullable();
            $table->string('real_firstname', 150)->nullable();
            $table->string('real_lastname', 150)->nullable();

            // Tulajdonos / kennel
            $table->string('owner_kennel', 150)->nullable();

            // Név sorrend
            $table->unsignedBigInteger('name_order_id')->nullable();

            // Születési adatok
            $table->date('real_dob')->nullable();
            $table->enum('real_sex', ['M', 'F', 'U'])->nullable();

            // Színek
            $table->string('real_color', 100)->nullable();
            $table->string('raw_color', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('birth_color', 100)->nullable();
            $table->string('official_color', 100)->nullable();

            // Fajta
            $table->string('real_breed', 100)->nullable();

            // Kennel prefix/suffix
            $table->string('kennel_prefix', 255)->nullable();
            $table->string('kennel_suffix', 255)->nullable();

            // Címek
            $table->longText('titles_json')->nullable();

            // Országok
            $table->string('real_origin_country', 10)->nullable();
            $table->string('real_standing_country', 10)->nullable();

            // Tenyészto / tulajdonos
            $table->string('real_breeder', 255)->nullable();
            $table->string('real_owner', 255)->nullable();
            $table->string('real_kennel', 255)->nullable();

            // Meta
            $table->string('found_on', 255)->nullable();
            $table->tinyInteger('confidence')->nullable();
            $table->dateTime('checked_at')->nullable();

            $table->text('notes')->nullable();

            // JAVÍTOTT timestamps
            $table->timestamps(); // created_at + updated_at helyesen

            // Indexek
            $table->index(['father_id']);
            $table->index(['mother_id']);
            $table->index(['source_dog_id']);
            $table->index(['source_reg_no']);
            $table->index(['real_name']);
            $table->index(['real_breed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedroo_dogs');
    }
};
