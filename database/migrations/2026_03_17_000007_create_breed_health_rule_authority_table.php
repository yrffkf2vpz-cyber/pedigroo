<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breed_health_rule_authority', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('breed_rule_health_id');
            $table->unsignedBigInteger('authority_id');

            $table->timestamps();

            // Rövidített indexnév
            $table->index(
                ['breed_rule_health_id', 'authority_id'],
                'bhra_rule_auth_idx'
            );

            // FK-k
            $table->foreign('breed_rule_health_id')
                ->references('id')
                ->on('pd_breed_rules_health')
                ->onDelete('cascade');

            $table->foreign('authority_id')
                ->references('id')
                ->on('pd_authorities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breed_health_rule_authority');
    }
};