<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dogs', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('pd_breeds')->nullOnDelete();
            $table->foreign('owner_id')->references('id')->on('pd_owners')->nullOnDelete();
            $table->foreign('kennel_id')->references('id')->on('pd_kennels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dogs', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['kennel_id']);
        });
    }
};