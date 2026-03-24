<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pending_dogs', function (Blueprint $table) {
            $table->id();

            // Audit
            $table->unsignedBigInteger('created_from_pedroo_id')->nullable();

            // Canonical dog fields
            $table->string('name')->nullable();
            $table->string('prefix')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();

            $table->unsignedBigInteger('kennel_id')->nullable();
            $table->unsignedBigInteger('breed_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();

            $table->string('sex')->nullable();
            $table->date('dob')->nullable();

            // RegNo canonical fields
            $table->string('reg_no')->nullable();
            $table->string('reg_no_clean')->nullable();
            $table->string('reg_prefix')->nullable();
            $table->string('reg_number')->nullable();
            $table->integer('reg_year')->nullable();
            $table->string('reg_country')->nullable();
            $table->string('reg_issuer')->nullable();

            // Colors
            $table->string('color')->nullable();
            $table->string('official_color')->nullable();
            $table->string('birth_color')->nullable();

            // Health JSON
            $table->json('health')->nullable();

            // Confidence
            $table->float('confidence')->default(0);

            // Pending logic
            $table->enum('activation_status', ['pending', 'activated', 'expired'])->default('pending');
            $table->string('activation_token', 64)->nullable();
            $table->string('pending_reason')->nullable();

            // Napra pontos 15 év
            $table->date('protected_until');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pending_dogs');
    }
};