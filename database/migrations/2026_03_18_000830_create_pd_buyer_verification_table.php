<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_buyer_verification', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik userhez tartozik
            $table->unsignedBigInteger('user_id')->unique();

            // ellenorzési eredmények
            $table->boolean('email_reputable')->default(true);
            $table->boolean('ip_reputable')->default(true);
            $table->boolean('disposable_email')->default(false);
            $table->boolean('bot_suspected')->default(false);

            // ország, IP, device fingerprint
            $table->string('country', 50)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('device_fingerprint', 255)->nullable();

            // kockázati szint: low / medium / high
            $table->string('risk_level', 20)->default('low');

            // metaadatok
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_buyer_verif_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_buyer_verification');
    }
};