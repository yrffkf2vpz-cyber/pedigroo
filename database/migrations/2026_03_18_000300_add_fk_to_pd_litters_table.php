<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_litters', function (Blueprint $table) {
            $table->foreign('sire_id')
                ->references('id')
                ->on('pd_dogs')
                ->nullOnDelete();

            $table->foreign('dam_id')
                ->references('id')
                ->on('pd_dogs')
                ->nullOnDelete();

            $table->foreign('breeder_id')
                ->references('id')
                ->on('pd_breeders')
                ->nullOnDelete();

            $table->foreign('kennel_id')
                ->references('id')
                ->on('pd_kennels')
                ->nullOnDelete();

            $table->foreign('country_id')
                ->references('id')
                ->on('pd_countries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_litters', function (Blueprint $table) {
            $table->dropForeign(['sire_id']);
            $table->dropForeign(['dam_id']);
            $table->dropForeign(['breeder_id']);
            $table->dropForeign(['kennel_id']);
            $table->dropForeign(['country_id']);
        });
    }
};