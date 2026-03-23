<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_parents', function (Blueprint $table) {
            $table->foreign('dog_id')->references('id')->on('pd_dogs')->cascadeOnDelete();
            $table->foreign('sire_id')->references('id')->on('pd_dogs')->nullOnDelete();
            $table->foreign('dam_id')->references('id')->on('pd_dogs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_parents', function (Blueprint $table) {
            $table->dropForeign(['dog_id']);
            $table->dropForeign(['sire_id']);
            $table->dropForeign(['dam_id']);
        });
    }
};