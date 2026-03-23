<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_litter_puppies', function (Blueprint $table) {
            $table->foreign('litter_id')
                ->references('id')
                ->on('pd_litters')
                ->cascadeOnDelete();

            $table->foreign('dog_id')
                ->references('id')
                ->on('pd_dogs')
                ->nullOnDelete();

            $table->foreign('sold_country_id')
                ->references('id')
                ->on('pd_countries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_litter_puppies', function (Blueprint $table) {
            $table->dropForeign(['litter_id']);
            $table->dropForeign(['dog_id']);
            $table->dropForeign(['sold_country_id']);
        });
    }
};