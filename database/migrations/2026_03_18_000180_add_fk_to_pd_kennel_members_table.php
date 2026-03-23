<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_kennel_members', function (Blueprint $table) {
            $table->foreign('kennel_id')->references('id')->on('pd_kennels')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('pd_owners')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_kennel_members', function (Blueprint $table) {
            $table->dropForeign(['kennel_id']);
            $table->dropForeign(['owner_id']);
        });
    }
};