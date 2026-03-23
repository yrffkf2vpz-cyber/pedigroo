<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_puppy_waitlist', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('pd_inquiries', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('kennel_id')->references('id')->on('pd_kennels')->cascadeOnDelete();
        });

        Schema::table('pd_buyer_verification', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_puppy_waitlist', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pd_inquiries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['kennel_id']);
        });

        Schema::table('pd_buyer_verification', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};