<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_breed_rules_health', function (Blueprint $table) {
            // Index elokészítés (ha még nincs)
            $table->index('breed_id');

            // Foreign key hozzáadása
            $table->foreign('breed_id')
                ->references('id')
                ->on('pd_breeds')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pd_breed_rules_health', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropIndex(['breed_id']);
        });
    }
};