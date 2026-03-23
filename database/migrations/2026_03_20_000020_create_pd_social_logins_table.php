<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_social_logins', function (Blueprint $table) {
            $table->bigIncrements('id');

            // kapcsolódó user (community / buyer / bárki)
            $table->unsignedBigInteger('user_id');

            // provider: facebook / google / apple / magic_link
            $table->string('provider', 50);

            // provider oldali user azonosító
            $table->string('provider_id', 255);

            $table->timestamps();

            $table->unique(['provider', 'provider_id'], 'uniq_pd_social_provider_user');
            $table->index(['user_id'], 'idx_pd_social_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_social_logins');
    }
};