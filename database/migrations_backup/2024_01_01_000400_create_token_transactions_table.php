<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('reason'); // media_upload, weekly_reward, judge_bonus, stb.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_transactions');
    }
};