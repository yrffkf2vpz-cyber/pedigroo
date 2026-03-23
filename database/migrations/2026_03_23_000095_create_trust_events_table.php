<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('type'); 
            // invitation, token_give, token_loan, token_repay, activity, ai_positive, ai_neutral

            $table->integer('amount'); // score növekedés (mindig pozitív)
            $table->json('meta')->nullable(); // extra adatok

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_events');
    }
};
