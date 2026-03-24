<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dogs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Lineage
            $table->unsignedBigInteger('sire_id')->nullable();
            $table->unsignedBigInteger('dam_id')->nullable();

            // Identity
            $table->string('registered_name', 255);
            $table->string('call_name', 150)->nullable();
            $table->string('registration_number', 50)->nullable();

            // Biological data
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('sex_id')->nullable();

            // Breed & origin
            $table->unsignedBigInteger('breed_id')->nullable();
            $table->unsignedBigInteger('origin_country_id')->nullable();
            $table->unsignedBigInteger('standing_country_id')->nullable();

            // Ownership
            $table->unsignedBigInteger('breeder_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('kennel_id')->nullable();

            // State
            $table->unsignedBigInteger('status_id')->nullable();
            $table->boolean('needs_review')->default(false);
            $table->enum('history_classification', ['modern', 'historical', 'legacy'])->nullable();

            $table->timestamps();

            // Self-referencing lineage
            $table->foreign('sire_id')->references('id')->on('pd_dogs')->onDelete('set null');
            $table->foreign('dam_id')->references('id')->on('pd_dogs')->onDelete('set null');

            // Foreign keys
            $table->foreign('breed_id')->references('id')->on('pd_breeds')->onDelete('set null');
            $table->foreign('origin_country_id')->references('id')->on('pd_countries')->onDelete('set null');
            $table->foreign('standing_country_id')->references('id')->on('pd_countries')->onDelete('set null');

            $table->foreign('breeder_id')->references('id')->on('pd_breeders')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('pd_owners')->onDelete('set null');
            $table->foreign('kennel_id')->references('id')->on('pd_kennels')->onDelete('set null');

            $table->foreign('status_id')->references('id')->on('pd_dog_statuses')->onDelete('set null');

            $table->index(['breed_id', 'sex_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dogs');
    }
};
