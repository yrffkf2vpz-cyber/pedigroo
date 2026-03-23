<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_buyer_profiles', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('pd_family_access_log', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('pd_user_trust_flags', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_buyer_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pd_family_access_log', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pd_user_trust_flags', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};