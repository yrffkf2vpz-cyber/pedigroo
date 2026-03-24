<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Meghívásos rendszer
            $table->foreignId('invited_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->integer('invite_quota')->default(3);

            // Alap adatok
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Admin flag (késobb role rendszer váltja ki)
            $table->boolean('super_admin')->default(false);

            // Felhasználói állapot
            $table->boolean('is_active')->default(true);

            // Laravel/Lumen alap mezok
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};