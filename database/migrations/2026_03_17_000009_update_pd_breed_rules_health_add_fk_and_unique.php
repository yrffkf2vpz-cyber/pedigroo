<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_breed_rules_health', function (Blueprint $table) {

            // 1) Típus javítása
            $table->unsignedBigInteger('breed_id')->change();

            // 2) FK hozzáadása (indexet NEM hozunk létre külön!)
            $table->foreign('breed_id', 'brh_breed_fk')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');

            // 3) Egyediség biztosítása
            $table->unique(['breed_id', 'test_code'], 'brh_breed_test_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pd_breed_rules_health', function (Blueprint $table) {

            $table->dropForeign('brh_breed_fk');
            $table->dropUnique('brh_breed_test_unique');

            // Ha kell, visszaállítható:
            // $table->integer('breed_id')->change();
        });
    }
};