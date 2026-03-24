<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_pending_dogs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('created_from_pedroo_id')->nullable();

            $table->string('name', 255)->nullable();
            $table->string('prefix', 255)->nullable();
            $table->string('firstname', 255)->nullable();
            $table->string('lastname', 255)->nullable();

            $table->unsignedBigInteger('kennel_id')->nullable();
            $table->unsignedBigInteger('breed_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();

            $table->string('sex', 255)->nullable();
            $table->date('dob')->nullable();

            $table->string('reg_no', 255)->nullable();
            $table->string('reg_no_clean', 255)->nullable();
            $table->string('reg_prefix', 255)->nullable();
            $table->string('reg_number', 255)->nullable();
            $table->integer('reg_year')->nullable();
            $table->string('reg_country', 255)->nullable();
            $table->string('reg_issuer', 255)->nullable();

            $table->string('color', 255)->nullable();
            $table->string('official_color', 255)->nullable();
            $table->string('birth_color', 255)->nullable();

            $table->json('health')->nullable();

            $table->double('confidence')->default(0);

            $table->enum('activation_status', ['pending', 'activated', 'expired'])
                  ->default('pending');

            $table->string('activation_token', 64)->nullable();
            $table->string('pending_reason', 255)->nullable();

            $table->date('protected_until');

            $table->timestamps();

            // Indexek
            $table->index(['breed_id']);
            $table->index(['owner_id']);
            $table->index(['kennel_id']);
            $table->index(['activation_status']);
            $table->index(['reg_no_clean']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_pending_dogs');
    }
};
