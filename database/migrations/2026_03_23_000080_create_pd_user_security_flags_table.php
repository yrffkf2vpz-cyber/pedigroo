<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_security_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('flag'); // suspicious_user, brute_force, etc.
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'flag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_security_flags');
    }
};
