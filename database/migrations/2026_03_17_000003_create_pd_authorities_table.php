<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_authorities', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Pl. "FCI", "AKC", "KC", "UKC", "CKC", "ANKC"
            $table->string('code', 20)->unique();

            // Pl. "Fédération Cynologique Internationale"
            $table->string('name', 255);

            // Ország (pl. Belgium, USA, UK)
            $table->string('country', 100)->nullable();

            // Weboldal
            $table->string('website', 255)->nullable();

            // Aktív-e (vannak inaktív kennelklubok is)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_authorities');
    }
};