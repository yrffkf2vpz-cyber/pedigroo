<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_behavior_results', function (Blueprint $table) {
            $table->foreign('behavior_test_id')
                ->references('id')->on('pd_dog_behavior_tests')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_behavior_results', function (Blueprint $table) {
            $table->dropForeign(['behavior_test_id']);
        });
    }
};